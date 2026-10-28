<?php
require_once('../../project/headerPage/header.php');
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

  <!-- Shop Section -->
  <section class="shop-section">
    <div class="breadcrumb">
      <a href="../../project/homePage/home.php">Home</a> / <span>Shop</span>
    </div>

    <div class="shop-header">
      <p>Showing 6 results</p>
      <select id="sortSelect" onchange="changeCategory(this.value)">
        <option value="all">All Categories</option>
        <?php
        // Ensure DB connection exists. header.php already includes db.php, but include again if needed.
        if (!isset($conn)) {
          require_once(__DIR__ . '/../db.php');
        }

        // Helper: resolve image path stored in DB to a usable src relative to this file (shopPage)
        function resolveImagePath($img, $fallback = null)
        {
          $img = trim((string) $img);
          if ($img === '') {
            return $fallback ?: '../photos/placeholder.png';
          }
          // absolute URL or root-relative path
          if (preg_match('#^https?://#i', $img) || strpos($img, '/') === 0) {
            return $img;
          }
          // check common locations on disk relative to this file
          $candidates = [
            __DIR__ . '/../' . $img,
            __DIR__ . '/../photos/' . $img,
            __DIR__ . '/' . $img,
          ];
          foreach ($candidates as $idx => $candidate) {
            if (file_exists($candidate)) {
              // Map candidate back to a web-relative path from this PHP file
              if ($idx === 0)
                return '../' . $img;
              if ($idx === 1)
                return '../photos/' . $img;
              return $img; // same directory
            }
          }
          // Last resort: assume image lives in ../photos/
          return '../photos/' . ltrim($img, '/');
        }

        // Attempt to read categories from a categories table first, fallback to distinct category from products
        $cats = [];
        $res = $conn->query("SELECT id, name FROM categories WHERE name IS NOT NULL AND name != '' ORDER BY name ASC");
        if ($res && $res->num_rows > 0) {
          while ($r = $res->fetch_assoc()) {
            $cats[] = $r['name'];
          }
        } else {
          // fallback to products table distinct categories
          $res2 = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
          if ($res2) {
            while ($r = $res2->fetch_assoc()) {
              $cats[] = $r['category'];
            }
          }
        }

        if (!empty($cats)) {
          // ensure unique and sorted
          $cats = array_values(array_unique($cats));
          sort($cats, SORT_STRING | SORT_FLAG_CASE);
          $currentSlug = isset($_GET['category']) ? preg_replace('/[^a-z0-9_\-]/', '', strtolower($_GET['category'])) : 'all';
          $hasPremium = false;
          foreach ($cats as $cat) {
            // create a safe slug/value used by JS and URLs
            $san = preg_replace('/[^a-z0-9_\-]/', '', strtolower($cat));
            if ($san === 'premium')
              $hasPremium = true;
            $label = htmlspecialchars(ucfirst($cat));
            $sel = ($currentSlug === $san) ? ' selected' : '';
            echo "<option value=\"$san\"$sel>$label</option>";
          }
          // Always offer a premium series filter even if not present in categories
          if (!$hasPremium) {
            $sel = ($currentSlug === 'premium') ? ' selected' : '';
            echo "<option value=\"premium\"$sel>Premium Series</option>";
          }
        } else {
          // default hard-coded options when DB has none
          $defaults = ['wedding' => 'Wedding', 'birthday' => 'Birthday', 'anniversary' => 'Anniversary', 'forher' => 'For Her', 'forhim' => 'For Him', 'forkids' => 'For Kids', 'premium' => 'Premium Series'];
          foreach ($defaults as $val => $lbl) {
            $sel = (isset($_GET['category']) && $_GET['category'] === $val) ? ' selected' : '';
            echo "<option value=\"$val\"$sel>$lbl</option>";
          }
        }
        ?>
      </select>
    </div>

    <div class="product-grid" id="productGrid">
      <?php
      // Fetch products from DB, optionally filter by ?category= (slug) or ?category_id=
      $db_products = [];
      $filter = isset($_GET['category']) ? $_GET['category'] : null;
      $filterId = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

      // Build lookup from available categories (we filled $cats earlier when generating the select)
      $lookup = [];
      foreach ($cats as $c) {
        $key = preg_replace('/[^a-z0-9_\-]/', '', strtolower($c));
        $lookup[$key] = $c;
      }

      $mappedFilter = null;
      $isPremiumFilter = false;
      // If category_id present and categories table exists, resolve to name
      if ($filterId > 0) {
        // Try to fetch name from categories table
        $stmtC = $conn->prepare("SELECT name FROM categories WHERE id = ? LIMIT 1");
        if ($stmtC) {
          $stmtC->bind_param('i', $filterId);
          $stmtC->execute();
          $resC = $stmtC->get_result();
          if ($resC && $resC->num_rows > 0) {
            $rC = $resC->fetch_assoc();
            $mappedFilter = $rC['name'];
          }
        }
      }

      if (!$mappedFilter && $filter) {
        $filterKey = preg_replace('/[^a-z0-9_\-]/', '', strtolower($filter));
        if ($filterKey === 'premium') {
          $isPremiumFilter = true;
        } elseif (isset($lookup[$filterKey])) {
          $mappedFilter = $lookup[$filterKey];
        }
      }

      if ($isPremiumFilter) {
        // Show only premium products (include is_premium flag)
        $resP = $conn->query("SELECT id, name, category, description, includes, price, img, IFNULL(is_premium,0) AS is_premium FROM products WHERE IFNULL(is_premium,0)=1 ORDER BY created_at DESC");
      } elseif ($mappedFilter) {
        $stmt = $conn->prepare("SELECT id, name, category, description, includes, price, img, IFNULL(is_premium,0) AS is_premium FROM products WHERE category = ? ORDER BY created_at DESC");
        $stmt->bind_param('s', $mappedFilter);
        $stmt->execute();
        $resP = $stmt->get_result();
      } else {
        $resP = $conn->query("SELECT id, name, category, description, includes, price, img, IFNULL(is_premium,0) AS is_premium FROM products ORDER BY created_at DESC");
      }

      if ($resP) {
        while ($row = $resP->fetch_assoc()) {
          $db_products[] = $row;
        }
      }

      if (!empty($db_products)):
        foreach ($db_products as $idx => $p):
          $catClassSafe = preg_replace('/[^a-z0-9_\-]/', '', strtolower($p['category']));
          // If this product is marked premium in DB, add a 'premium' class so client-side filtering can match it
          $isPremiumClass = (isset($p['is_premium']) && intval($p['is_premium']) === 1) ? ' premium' : '';
          $img = htmlspecialchars(resolveImagePath($p['img'] ?? '', '../photos/placeholder.png'));
          $name = htmlspecialchars($p['name']);
          // normalize price stored in DB (handles values like "5,000" or "₹5,000")
          $rawPrice = $p['price'] ?? '';
          $priceNumeric = 0;
          if (is_numeric($rawPrice)) {
            $priceNumeric = $rawPrice + 0;
          } else {
            $clean = preg_replace('/[^0-9.]/', '', (string) $rawPrice);
            if ($clean !== '')
              $priceNumeric = (float) $clean;
          }
          $priceFmt = '₹' . number_format($priceNumeric, 0);
          $desc = htmlspecialchars($p['description']);
          $includesHtml = $p['includes']; // may contain HTML stored by admin
          ?>
          <div class="product <?php echo $catClassSafe . $isPremiumClass; ?>" data-id="<?php echo (int) $p['id']; ?>"
            data-index="<?php echo $idx; ?>" data-price="<?php echo $priceNumeric; ?>" data-desc="<?php echo $desc; ?>">
            <div class="product-img">
              <img src="<?php echo $img; ?>" alt="<?php echo $name; ?>">
              <div class="hover-icons">
                <span class="cart-btn">🛒</span>
                <span class="view-btn">🔍</span>
              </div>
            </div>
            <h3><?php echo $name; ?></h3>
            <p class="new-price"><?php echo $priceFmt; ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No products found.</p>
      <?php endif; ?>
    </div>

    <script>
      // Build JS products array so existing shop2.js modal/cart logic (which relies on index) works.
      var products = [
        <?php foreach ($db_products as $p):
          $imgJs = addslashes(resolveImagePath($p['img'] ?? '', '../photos/placeholder.png'));
          $nameJs = addslashes($p['name']);
          // compute numeric price per-product to avoid using a stale variable
          $rawPrice = $p['price'] ?? '';
          $priceNumericJs = 0;
          if (is_numeric($rawPrice)) {
            $priceNumericJs = $rawPrice + 0;
          } else {
            $clean = preg_replace('/[^0-9.]/', '', (string) $rawPrice);
            if ($clean !== '')
              $priceNumericJs = (float) $clean;
          }
          $priceJs = '₹' . number_format($priceNumericJs, 0);
          $descJs = addslashes($p['description']);
          // If includes looks like plain comma-separated text, convert to a simple <ul> for modal display.
          $includesRaw = $p['includes'] ?? '';
          if (trim($includesRaw) !== '' && !preg_match('/<[a-z][\s\S]*>/i', $includesRaw)) {
            $parts = array_filter(array_map('trim', explode(',', $includesRaw)));
            if (!empty($parts)) {
              $listHtml = '<ul>' . implode('', array_map(function ($it) {
                return '<li>' . htmlspecialchars($it) . '</li>';
              }, $parts)) . '</ul>';
              $includesJs = addslashes($listHtml);
            } else {
              $includesJs = addslashes($includesRaw);
            }
          } else {
            $includesJs = addslashes($includesRaw);
          }
          ?>
                      {
            name: "<?php echo $nameJs; ?>",
            price: "<?php echo $priceJs; ?>",
            img: "<?php echo $imgJs; ?>",
            desc: "<?php echo $descJs; ?>",
            includes: `<?php echo $includesJs; ?>`
          },
        <?php endforeach; ?>
      ];
    </script>

  </section>


  <!-- Single Modal (only one per page) -->
  <div id="productModal" class="modal">
    <div class="modal-content">
      <span id="modalClose">&times;</span>
      <div class="modal-body">
        <div class="modal-left">
          <img id="modalImg" src="" class="cart-item-img" alt="Product Image" />
        </div>
        <div class="modal-right">
          <div class="modal-header-right">
            <h3 id="modalTitle"></h3>
            <div class="modal-price-row">
              <span id="modalPrice" class="modal-price"></span>
              <span id="modalOriginalPrice" class="modal-original-price"></span>
            </div>
            <div class="modal-divider"></div>
            <button id="modalAdd" class="add-cart-btn">ADD TO CART</button>
          </div>
          <div id="modalDesc" class="modal-desc"></div>
          <div class="modal-includes-wrapper">
            <h4 class="modal-includes-heading">Hamper includes</h4>
            <div id="modalIncludes" class="modal-includes"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div id="cartToast">✅ Box added to cart successfully.</div>

  <script src="../../project/shopPage/shop2.js"></script>
</body>

</html>
<?php
require_once('../footerPage/footer.php');
?>