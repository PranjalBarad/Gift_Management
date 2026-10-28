<?php
require_once('../../project/headerPage/header.php');
require_once('../../project/db.php');

// Load categories for select: prefer `categories` table, fallback to distinct product categories
$categories = [];
$res = $conn->query("SELECT id, name FROM categories WHERE name IS NOT NULL AND name != '' ORDER BY name ASC");
if ($res && $res->num_rows > 0) {
  while ($r = $res->fetch_assoc()) {
    $categories[] = $r['name'];
  }
} else {
  $cres = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''");
  if ($cres) {
    while ($r = $cres->fetch_assoc())
      $categories[] = $r['category'];
  }
}

// Fetch products from database (apply category filter if provided)
$db_products = [];
$filter = isset($_GET['category']) ? $_GET['category'] : null;
// Map sanitized category (from URL or header links) back to real DB category values
$mappedFilter = null;
if ($filter) {
  $lookup = [];
  foreach ($categories as $c) {
    $key = preg_replace('/[^a-z0-9_\-]/', '', strtolower($c));
    $lookup[$key] = $c;
  }
  $filterKey = preg_replace('/[^a-z0-9_\-]/', '', strtolower($filter));
  if (isset($lookup[$filterKey])) {
    $mappedFilter = $lookup[$filterKey];
  }
}

if ($mappedFilter) {
  $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
  $stmt->bind_param('s', $mappedFilter);
  $stmt->execute();
  $res = $stmt->get_result();
} else {
  $res = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
}
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $db_products[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop</title>
  <link rel="stylesheet" href="../../project/shopPage/shop2.css">
</head>

<body>

  <body>

    <!-- Shop Section -->
    <section class="shop-section">
      <div class="breadcrumb">
        <a href="../../project/homePage/home.php">Home</a> / <span>Shop</span>
      </div>

      <div class="shop-header">
        <p id="resultsText">Showing <?php echo count($db_products); ?> results</p>
        <select id="sortSelect" onchange="changeCategory(this.value)">
          <option value="all">Default Sorting</option>
          <?php foreach ($categories as $cat):
            $val = htmlspecialchars($cat);
            $label = htmlspecialchars(ucfirst($cat));
            ?>
            <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="product-grid" id="productGrid">
        <?php if (!empty($db_products)): ?>
          <?php foreach ($db_products as $p):
            $catClass = htmlspecialchars($p['category']);
            $img = htmlspecialchars($p['img'] ?: '../../project/photos/placeholder.png');
            $name = htmlspecialchars($p['name']);
            $price = htmlspecialchars('₹' . number_format($p['price'], 0));
            ?>
            <?php $catClassSafe = preg_replace('/[^a-z0-9_\-]/', '', strtolower($p['category'])); ?>
            <div class="product <?php echo $catClassSafe; ?>" data-price="<?php echo $p['price']; ?>">
              <div class="product-img">
                <img src="<?php echo $img; ?>" alt="<?php echo $name; ?>">
                <div class="hover-icons">
                  <span class="cart-btn" data-name="<?php echo $name; ?>">🛒</span>
                  <span class="view-btn">🔍</span>
                </div>
              </div>
              <h3><?php echo $name; ?></h3>
              <p><span class="new-price"><?php echo $price; ?></span></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- fallback static content if DB empty -->
          <p>No products found.</p>
        <?php endif; ?>

        <!-- end product-grid -->
    </section>
    <script src="../../project/shopPage/shop2.js"></script>
  </body>

</html>
<?php
require_once('../footerPage/footer.php');
?>