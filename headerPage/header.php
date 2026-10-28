<?php
// Ensure session is started so header can access cart and user state without warnings
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Load distinct categories for navigation dropdown. Prefer a 'categories' table if present.
// nav_categories will hold arrays with keys: id, name
$nav_categories = [];
require_once(__DIR__ . '/../db.php');

// compute cart count (sum of qty) safely
$cart_count = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $ci) {
    $cart_count += isset($ci['qty']) ? intval($ci['qty']) : 1;
  }
}

// Try categories table first (store id and name). If not present, fallback to distinct category values from products.
$res = $conn->query("SELECT id, name FROM categories WHERE name IS NOT NULL AND name != '' ORDER BY name ASC");
if ($res && $res->num_rows > 0) {
  while ($r = $res->fetch_assoc()) {
    $nav_categories[] = ['id' => (int) $r['id'], 'name' => $r['name']];
  }
} else {
  // Fallback to distinct category values from products (no id available)
  $res2 = $conn->query("SELECT DISTINCT TRIM(category) AS category FROM products WHERE IFNULL(category,'')!='' ORDER BY category ASC");
  if ($res2) {
    while ($r = $res2->fetch_assoc()) {
      $nav_categories[] = ['id' => 0, 'name' => $r['category']];
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Buze Co</title>
  <link rel="stylesheet" href="../../project/headerPage/header.css" />
  <!-- removed incorrect stylesheet link to a PHP file -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- badge removed: no numeric dot on cart icon -->
</head>

<body>
  <!-- Header -->
  <header>
    <nav>
      <div class="logo">
        Buze Co
      </div>
      <ul class="nav_items">
        <li><a href="../../project/homePage/home.php" class="nav_items_head">HOME</a></li>
        <li class="dropdown"><a href="../../project/shopPage/shop2.php" class="nav_items_head">SHOP ▾</a>
          <ul class="dropdown_ele">
            <?php if (!empty($nav_categories)): ?>
              <?php
              // Track whether a 'premium' slug already exists to avoid duplicate entry
              $hasPremiumNav = false;
              foreach ($nav_categories as $cat):
                // $cat is ['id'=>int,'name'=>string]
                $name = $cat['name'];
                $id = isset($cat['id']) ? (int) $cat['id'] : 0;
                $san = preg_replace('/[^a-z0-9_\-]/', '', strtolower($name));
                if ($san === 'premium')
                  $hasPremiumNav = true;
                $safeUrl = urlencode($san);
                $label = htmlspecialchars(ucfirst($name));
                if ($id > 0):
                  $link = "../../project/shopPage/shop2.php?category_id=" . $id;
                else:
                  $link = "../../project/shopPage/shop2.php?category=" . $safeUrl;
                endif;
                ?>
                <li><a href="<?php echo $link; ?>"><?php echo $label; ?></a>
                </li>
              <?php endforeach; ?>
              <?php
              // Always include Premium Series as an option if it wasn't present in categories
              if (!$hasPremiumNav):
                ?>
                <li><a href="../../project/shopPage/shop2.php?category=premium">Premium Series</a></li>
                <?php
              endif;
              ?>
            <?php else: ?>
              <li><a href="../../project/shopPage/shop2.php?category=wedding">Wedding</a></li>
              <li><a href="../../project/shopPage/shop2.php?category=birthday">Birthday</a></li>
              <li><a href="../../project/shopPage/shop2.php?category=anniversary">Anniversary</a></li>
              <li><a href="../../project/shopPage/shop2.php?category=forher">For Her</a></li>
              <li><a href="../../project/shopPage/shop2.php?category=forhim">For Him</a></li>
              <li><a href="../../project/shopPage/shop2.php?category=forkids">For Kids</a></li>
              <li><a href="../../project/shopPage/shop2.php?category=premium">Premium Series</a></li>
            <?php endif; ?>
          </ul>
        </li>
        <li><a href="../../project/aboutusPage/aboutus.php" class="nav_items_head">ABOUT US</a></li>
        <!-- <li><a href="../../project/contactusPage/contactus.php" class="nav_items_head">MY ORDERS</a></li> -->
        <li><a href="../../project/orderPage/order.php" class="nav_items_head">MY ORDERS</a></li>
        <div class="last_logo">
          <!-- profile icon: link to profile page -->
           <a href="../../project/cartPage/cart.php" class="cart-link nav_items_head">
            <i class="fa-solid fa-cart-shopping"></i>
          </a>
          <a href="../../project/logIn/profile.php" class="fa-solid fa-user nav_items_head"></a>
          
        </div>
      </ul>
    </nav>
  </header>

</body>

</html>




<!-- <div class="nav_items"></div>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">About us</a></li>
        <li class="dropdown">
          <a href="#">Skincare ▾</a>
          <ul class="dropdown-content">
            <li><a href="#">Face Wash</a></li>
            <li><a href="#">Toner/Serum</a></li>
            <li><a href="#">Moisturizer</a></li>
            <li><a href="#">Sunscreen</a>
          </ul>
        </li>
        <li><a href="#" class="fa-solid fa-user"></a></li>
        </li><a href="#" class="fa-solid fa-user"></a></li>
      </ul>
       -->