<?php
session_start();
require_once('../../project/headerPage/header.php');

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
<div class="cart-section">
    <div class="cart-breadcrumb">
        <a href="../shopPage/shop2.php">Shop </a> / <span> Shopping Cart</span>
    </div>
    
    <?php if (!empty($_SESSION['cart'])): ?>
        <?php
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += intval(str_replace('₹','',$item['price'])) * $item['qty'];
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
                            <?php foreach (explode('|', $item['includes']) as $inc): ?>
                                <li><?php echo htmlspecialchars($inc); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>

                        <form method="post" class="cart-qty-form">
                        <input type="hidden" name="idx" value="<?php echo $i; ?>">
                        <button type="submit" name="update_qty" value="<?php echo $item['qty']-1; ?>" class="qty-btn" <?php if($item['qty']<=1) echo 'disabled'; ?>>-</button>
                        <span style="padding:0 12px;font-size:1.1rem;"><?php echo $item['qty']; ?></span>
                        <button type="submit" name="update_qty" value="<?php echo $item['qty']+1; ?>" class="qty-btn">+</button>
                        </form>
                        <div class="cart-item-price"><?php echo htmlspecialchars($item['price']); ?></div>
                        <div class="cart-item-total">Total: ₹<?php echo intval(str_replace('₹','',$item['price'])) * $item['qty']; ?></div>
                        <a href="cart.php?remove=<?php echo $i; ?>" class="remove-btn">Remove</a>
                    </div>
                </div>
                <?php endforeach; ?>
                <a href="../shopPage/shop2.php" class="continue-btn">← CONTINUE SHOPPING</a>
            </div>
            <div class="cart-summary">
                <div class="cart-summary-title">Order Summary</div>
                <div class="cart-summary-row">
                    <span>Subtotal:</span>
                    <span>₹<?php echo $subtotal; ?></span>
                </div>
                <div class="cart-summary-row">
                    <span>Shipping:</span>
                    <span>₹0.00</span>
                </div>
                <div class="cart-summary-row cart-summary-total">
                    <span>Total:</span>
                    <span>₹<?php echo $subtotal; ?></span>
                </div>
                <button class="proceed-btn">PROCEED TO BUY</button>
            </div>
        </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
        <a href="../shopPage/shop2.php" class="continue-btn">← CONTINUE SHOPPING</a>
    <?php endif; ?>
</div>
<script src="cart.js"></script>
</body>
</html>
<?php require_once('../footerPage/footer.php'); ?>