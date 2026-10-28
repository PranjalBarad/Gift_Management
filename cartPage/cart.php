<?php
session_start();

// Determine if this is an AJAX request (client sets ajax=1) so we can return JSON
$isAjax = false;
if (isset($_POST['ajax']) && $_POST['ajax']) {
  $isAjax = true;
} elseif (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
  $isAjax = true;
}

// Ensure DB is available for both AJAX and full page requests
require_once('../../project/db.php');

// Only include the full header HTML for non-AJAX page loads
if (!$isAjax) {
  require_once('../../project/headerPage/header.php');
}

// Helper: parse a price string like "₹5,000" or "5,000.00" into a float 5000.00
function parse_price($s)
{
  if (!isset($s))
    return 0.0;
  $s = trim($s);
  // remove commas and any non-digit except dot
  $s = str_replace(',', '', $s);
  $s = preg_replace('/[^0-9.]/', '', $s);
  if ($s === '')
    return 0.0;
  return floatval($s);
}

function format_price_display($amount)
{
  // show no decimals for whole numbers, otherwise show 2 decimals
  if (floor($amount) == $amount) {
    return number_format($amount, 0, '.', ',');
  }
  return number_format($amount, 2, '.', ',');
}

// Add product to cart
if (isset($_POST['add_to_cart'])) {
  $product = [
    'name' => $_POST['name'],
    'price' => $_POST['price'],
    'img' => $_POST['img'],
    'desc' => $_POST['desc'] ?? '',
    'includes' => $_POST['includes'] ?? '',
    'qty' => 1
  ];
  // Check if product already in cart
  $found = false;
  if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as &$item) {
      if ($item['name'] === $product['name']) {
        $item['qty'] += 1;
        $found = true;
        break;
      }
    }
  }
  if (!$found) {
    $_SESSION['cart'][] = $product;
  }

  // If request came from AJAX, return JSON success instead of redirecting
  if ($isAjax) {
    // compute cart count
    $cart_count = 0;
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
      foreach ($_SESSION['cart'] as $ci) {
        $cart_count += isset($ci['qty']) ? intval($ci['qty']) : 1;
      }
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Added to cart', 'cart_count' => $cart_count]);
    exit;
  }

  header('Location: cart.php');
  exit;
}

// Remove product from cart
if (isset($_GET['remove'])) {
  $remove = $_GET['remove'];
  unset($_SESSION['cart'][$remove]);
  $_SESSION['cart'] = array_values($_SESSION['cart']);
  header('Location: cart.php');
  exit;
}

// Update quantity
if (isset($_POST['update_qty'])) {
  $idx = $_POST['idx'];
  $qty = max(1, intval($_POST['update_qty']));
  $_SESSION['cart'][$idx]['qty'] = $qty;
  header('Location: cart.php');
  exit;
}

// Check if user clicked "PROCEED TO BUY"
if (isset($_POST['proceed_to_buy'])) {
  if (!isset($_SESSION['user'])) {
    // Not logged in, redirect to login
    $_SESSION['redirect_after_login'] = '../cartPage/cart.php';
    header('Location: ../logIn/log.php');
    exit;
  } else {
    // Logged in, redirect to checkout page
    header('Location: ../cartPage/checkout.php');
    exit;
  }
}

// Ensure any missing 'includes' values are fetched from DB so cart shows them
if (!empty($_SESSION['cart']) && isset($conn)) {
  foreach ($_SESSION['cart'] as $idx => $citem) {
    if (empty($citem['includes'])) {
      // try to find product by name and fill 'includes'
      $pname = $conn->real_escape_string($citem['name']);
      $q = $conn->query("SELECT includes FROM products WHERE name = '$pname' LIMIT 1");
      if ($q && $q->num_rows > 0) {
        $r = $q->fetch_assoc();
        $_SESSION['cart'][$idx]['includes'] = $r['includes'];
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <link rel="stylesheet" href="../../project/cartPage/cart.css">
</head>

<body>
  <div class="cart-section">
    <div class="cart-breadcrumb">
      <a href="../shopPage/shop2.php">Shop </a> / <span> Shopping Cart</span>
    </div>

    <?php if (!empty($_SESSION['cart'])): ?>
      <?php
      $subtotal = 0.0;
      foreach ($_SESSION['cart'] as $item) {
        $price_num = parse_price($item['price']);
        $subtotal += $price_num * (isset($item['qty']) ? intval($item['qty']) : 1);
      }
      ?>
      <div class="cart-free-shipping">
        Congratulations! You are eligible for Free Shipping.
      </div>
      <div class="cart-main">
        <div class="cart-items">
          <?php foreach ($_SESSION['cart'] as $i => $item): ?>
            <div class="cart-item">
              <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="" class="cart-item-img">
              <div class="cart-item-details">
                <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>

                <?php if (!empty($item['includes'])): ?>
                  <ul class="cart-item-includes">
                    <?php foreach (explode(',', $item['includes']) as $inc): ?>
                      <?php $inc = trim($inc);
                      if ($inc === '')
                        continue; ?>
                      <li><?php echo htmlspecialchars($inc); ?></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>

                <form method="post" class="cart-qty-form">
                  <input type="hidden" name="idx" value="<?php echo $i; ?>">
                  <button type="submit" name="update_qty" value="<?php echo $item['qty'] - 1; ?>" class="qty-btn" <?php if ($item['qty'] <= 1)
                         echo 'disabled'; ?>>-</button>
                  <span style="padding:0 12px;font-size:1.1rem;"><?php echo $item['qty']; ?></span>
                  <button type="submit" name="update_qty" value="<?php echo $item['qty'] + 1; ?>" class="qty-btn">+</button>
                </form>
                <div class="cart-item-price"><?php echo htmlspecialchars($item['price']); ?></div>
                <div class="cart-item-total">Total:
                  ₹<?php echo format_price_display(parse_price($item['price']) * (isset($item['qty']) ? intval($item['qty']) : 1)); ?>
                </div>
                <a href="cart.php?remove=<?php echo $i; ?>" class="remove-btn">Remove</a>
              </div>
            </div>
          <?php endforeach; ?>
          <a href="../shopPage/shop2.php" class="continue-btn"> <span style="padding: 0 11px;">←</span>Continue
            Shopping</a>
        </div>
        <div class="cart-summary">
          <div class="cart-summary-title">Order Summary</div>
          <div class="cart-summary-row">
            <span>Subtotal:</span>
            <span>₹<?php echo format_price_display($subtotal); ?></span>
          </div>
          <div class="cart-summary-row">
            <span>Shipping:</span>
            <span>₹0.00</span>
          </div>
          <div class="cart-summary-row cart-summary-total">
            <span>Total:</span>
            <span>₹<?php echo format_price_display($subtotal); ?></span>
          </div>
          <form method="post" style="margin-top:20px;">
            <button class="proceed-btn" name="proceed_to_buy" type="submit">PROCEED TO BUY</button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <p id="empty">Your cart is empty.</p>
      <a href="../shopPage/shop2.php" class="continue-btn"><span style="padding: 0 11px;">←</span> Continue Shopping</a>
    <?php endif; ?>

  </div>
  <script src="cart.js"></script>
</body>

</html>
<?php require_once('../footerPage/footer.php'); ?>