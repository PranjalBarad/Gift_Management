<?php
// Require admin authentication
require_once __DIR__ . '/auth.php';
// Connect to the database and fetch products
include_once __DIR__ . '/../db.php';
$products = [];
$error = '';
$sql = "SELECT id, name, category, description, includes, price, IFNULL(is_premium,0) as is_premium FROM products";
if ($result = $conn->query($sql)) {
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
  $result->free();
} else {
  $error = $conn->error;
}

// Load categories from categories table if exists, otherwise derive from products
$categories = [];
$catRes = $conn->query("SHOW TABLES LIKE 'categories'");
if ($catRes && $catRes->num_rows > 0) {
  // load categories (do not fetch image paths here; images are stored but not used in this admin list)
  $r = $conn->query('SELECT id, name FROM categories ORDER BY name');
  if ($r) {
    while ($c = $r->fetch_assoc()) {
      $c['img'] = '';
      $categories[] = $c;
    }
    $r->free();
  }
} else {
  // derive unique categories from products
  $seen = [];
  foreach ($products as $p) {
    $cat = trim($p['category']);
    if ($cat !== '' && !isset($seen[$cat])) {
      $seen[$cat] = true;
      $categories[] = ['id' => 0, 'name' => $cat, 'img' => ''];
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - Buze Co Admin</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/script.js" defer></script>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🎁 Buze Co</h2>
    <a href="index.php">📊 Dashboard</a>
    <a href="products.php" class="active">📦 Products</a>
    <a href="gift_items.php">🎀 Gift Wrapper</a>
    <a href="orders.php">🛒 Orders</a>
    <a href="customers.php">👥 Customers</a>
    <a href="contactus.php">📨 Contact Us</a>
    <!-- <a href="notifications.php">🔔 Notifications</a> -->
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Products</h1>
      <div class="profile">
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="18" cy="18" r="18" fill="#E0E0E0" />
          <circle cx="18" cy="14" r="7" fill="#BDBDBD" />
          <ellipse cx="18" cy="27" rx="11" ry="6" fill="#BDBDBD" />
        </svg>
        <span>Admin</span>
      </div>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin:12px 0;">
      <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <button class="btn" onclick="openModal()">➕ Add Product</button>
        <button class="btn" id="toggleCategoryPanel" type="button">🗂️ Categories</button>
      </div>
      <div style="display:flex;gap:8px;align-items:center;">
        <button class="btn" id="markPremiumBtn" type="button">Mark Premium</button>
        <button class="btn" id="unmarkPremiumBtn" type="button" style="background:#6b7280">Unmark Premium</button>
      </div>
    </div>

    <!-- Collapsible Categories Panel -->
    <div id="categoryPanel" class="category-panel" style="display:none;">
      <div class="category-panel-inner">
        <div class="category-panel-header">
          <h3>Categories</h3>
          <button class="btn" id="closeCategoryPanel">Close</button>
        </div>
        <div id="categoryList" class="category-list">
          <!-- dynamically filled -->
        </div>
        <div class="category-panel-actions">
          <input id="panelNewCategory" placeholder="New category name" />
          <input id="panelNewCategoryImg" type="file" accept="image/*" />
          <button class="btn" id="panelAddCategory">Add Category</button>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table">
        <tr>
          <th><input type="checkbox" id="selectAll"></th>
          <th>ID</th>
          <th>Name</th>
          <th>Category</th>
          <th>Special</th>
          <th>Description</th>
          <th>Includes</th>
          <th>Price</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
        <?php if ($error): ?>
          <tr>
            <td colspan="9">Error loading products: <?= htmlspecialchars($error) ?></td>
          </tr>
        <?php elseif (empty($products)): ?>
          <tr>
            <td colspan="9">No products found.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($products as $p): ?>
            <tr data-id="<?= (int) $p['id'] ?>" data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
              data-category="<?= htmlspecialchars($p['category'], ENT_QUOTES) ?>"
              data-description="<?= htmlspecialchars($p['description'], ENT_QUOTES) ?>"
              data-includes="<?= htmlspecialchars($p['includes'], ENT_QUOTES) ?>"
              data-price="<?= htmlspecialchars($p['price'], ENT_QUOTES) ?>">
              <td><input type="checkbox" class="rowCheck" value="<?= (int) $p['id'] ?>"></td>
              <td><?= htmlspecialchars($p['id']) ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['category']) ?></td>
              <td><?php if (!empty($p['is_premium'])): ?><span
                    style="background:#fde047;color:#92400e;padding:4px 8px;border-radius:6px;font-weight:600;display:inline-block;">Special</span><?php else: ?>-<?php endif; ?>
              </td>
              <td><?= htmlspecialchars($p['description']) ?></td>
              <td>
                <?php
                $incText = trim((string) ($p['includes'] ?? ''));
                if ($incText === '') {
                  echo '-';
                } else {
                  // If it contains HTML tags, print as-is (admin-entered HTML). Otherwise treat as CSV and render as comma-separated text.
                  if (preg_match('/<[a-z][\s\S]*>/i', $incText)) {
                    echo $incText;
                  } else {
                    $parts = array_filter(array_map('trim', explode(',', $incText)));
                    if (!empty($parts)) {
                      // escape each part and join with comma + space
                      $escaped = array_map('htmlspecialchars', $parts);
                      echo implode(', ', $escaped);
                    } else {
                      echo htmlspecialchars($incText);
                    }
                  }
                }
                ?>
              </td>
              <td>₹<?= htmlspecialchars(number_format($p['price'])) ?></td>
              <td><button class="btn" onclick="editProduct(<?= (int) $p['id'] ?>)">Edit</button></td>
              <td><button class="btn" style="background:#e74c3c"
                  onclick="deleteProduct(<?= (int) $p['id'] ?>)">Delete</button></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="productModal">
    <div class="modal-content">
      <div class="modal-body">
        <h2>Add Product</h2>
        <div id="modalError" class="modal-error" style="display:none;"></div>
        <input type="text" id="productName" placeholder="Product Name">
        <div style="display:flex;gap:8px;align-items:center;">
          <select id="productCategory" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:6px;">
            <option value="">-- Select category --</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= htmlspecialchars($c['name']) ?>" data-id="<?= (int) $c['id'] ?>">
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <textarea id="productDescription" placeholder="Description"
          style="width:100%;padding:10px;margin:8px 0;border:1px solid #ddd;border-radius:6px;"></textarea>
        <input type="text" id="productIncludes" placeholder="Includes (comma separated)">
        <input type="number" id="productPrice" placeholder="Price">
        <!-- Premium is managed via bulk buttons; removed checkbox from form -->
        <label for="productImg" style="margin-top:10px;font-weight:600;display:block;">Product Image</label>
        <input type="file" id="productImg" accept="image/*" style="margin-bottom:10px;">
        <div id="imgPreview" class="img-preview" style="margin-bottom:10px;"></div>
      </div>
      <div class="modal-footer">
        <button class="btn" id="modalSaveBtn" onclick="saveProduct()">Save</button>
        <button class="btn" style="background:#888" onclick="closeModal()">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    // Edit product: fetch fresh data from server and populate modal
    window._originalProduct = null;
    function editProduct(id) {
      fetch('get_product.php?id=' + encodeURIComponent(id))
        .then(r => r.json())
        .then(data => {
          if (!data.success) { alert('Failed to load product: ' + (data.message || '')); return; }
          var p = data.product;
          document.getElementById('productName').value = p.name || '';
          var catSel = document.getElementById('productCategory');
          var catVal = p.category || '';
          // if category not present in select, add it so it can be selected
          if (catVal) {
            var found = false;
            for (var i = 0; i < catSel.options.length; i++) {
              if (catSel.options[i].value === catVal) { found = true; break; }
            }
            if (!found) {
              var opt = document.createElement('option'); opt.value = catVal; opt.textContent = catVal; catSel.appendChild(opt);
            }
            catSel.value = catVal;
          } else {
            catSel.value = '';
          }
          document.getElementById('productDescription').value = p.description || '';
          document.getElementById('productIncludes').value = p.includes || '';
          document.getElementById('productPrice').value = p.price || '';
          window.editingProductId = p.id;
          // store snapshot for change detection
          window._originalProduct = { id: p.id, name: p.name, category: p.category, description: p.description, includes: p.includes, price: p.price, is_premium: p.is_premium ? String(p.is_premium) : '0' };
          openModal();
        }).catch(function (err) { alert('Request failed: ' + err); });
    }

    // Delete product (demo only, does not persist)
    function deleteProduct(id) {
      if (!confirm('Are you sure you want to delete Product #' + id + '? This action cannot be undone.')) return;
      var fd = new FormData(); fd.append('id', id);
      fetch('delete_product.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(function (data) {
          if (!data || !data.success) { alert('Delete failed: ' + (data ? (data.message || 'unknown') : 'no response')); return; }
          // remove row from table
          var tr = document.querySelector('tr[data-id="' + id + '"]');
          if (tr) tr.parentNode.removeChild(tr);
        }).catch(function (err) { alert('Request failed: ' + err); });
    }
    // Save product via AJAX (create or update)
    function saveProduct() {
      var fd = new FormData();
      var id = window.editingProductId || 0;
      fd.append('id', id);
      fd.append('name', document.getElementById('productName').value.trim());
      fd.append('category', document.getElementById('productCategory').value.trim());
      fd.append('description', document.getElementById('productDescription').value.trim());
      fd.append('includes', document.getElementById('productIncludes').value.trim());
      fd.append('price', document.getElementById('productPrice').value.trim());
      var img = document.getElementById('productImg').files[0];
      var premiumEl = document.getElementById('productPremium');
      var hasPremiumControl = !!premiumEl;
      var isPremium = (hasPremiumControl && premiumEl.checked) ? '1' : '0';
      // only append is_premium if the control exists (so we don't overwrite server value unintentionally)
      if (hasPremiumControl) fd.append('is_premium', isPremium);
      // Compare with original data (if editing). If no changes and no image, skip submit
      if (id && window._originalProduct) {
        var same = (
          window._originalProduct.name === fd.get('name') &&
          window._originalProduct.category === fd.get('category') &&
          window._originalProduct.description === fd.get('description') &&
          window._originalProduct.includes === fd.get('includes') &&
          String(window._originalProduct.price) === String(fd.get('price'))
        );
        // include premium in comparison only if the control is present
        if (hasPremiumControl) {
          same = same && (String(window._originalProduct.is_premium) === String(isPremium));
        }
        if (same && !img) { alert('No changes made'); closeModal(); window.editingProductId = null; return; }
      }
      if (img) fd.append('img', img);

      var savingBtn = document.getElementById('modalSaveBtn');
      savingBtn.disabled = true; savingBtn.textContent = 'Saving...';

      fetch('save_product.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          savingBtn.disabled = false; savingBtn.textContent = 'Save';
          if (data.success) {
            // success: reload page to show new product in table
            location.reload();
          } else {
            alert('Save failed: ' + (data.message || 'unknown'));
          }
        }).catch(function (err) { savingBtn.disabled = false; savingBtn.textContent = 'Save'; alert('Request failed: ' + err); });
    }



    // When opening modal for add, clear fields
    function openModal() {
      if (!window.editingProductId) {
        document.getElementById('productName').value = '';
        document.getElementById('productCategory').value = '';
        document.getElementById('productDescription').value = '';
        document.getElementById('productIncludes').value = '';
        document.getElementById('productPrice').value = '';
        document.getElementById('productImg').value = '';
        document.getElementById('imgPreview').innerHTML = '';
      }
      document.getElementById('productModal').style.display = 'flex';
    }

    // helper to show inline errors in modal
    function showModalError(html) {
      var box = document.getElementById('modalError');
      if (!box) return; box.style.display = 'block'; box.innerText = '';
      // if the server returned HTML, show a trimmed preview
      if (typeof html === 'string' && html.trim().startsWith('<')) {
        // show a short snippet, and include full in console
        box.innerText = 'Server returned HTML error. Check console for full response.';
        console.error('Server response (HTML):\n', html);
      } else {
        box.innerText = html || 'An unexpected error occurred';
      }
    }

    // clear modal error helper
    function clearModalError() { var b = document.getElementById('modalError'); if (b) { b.style.display = 'none'; b.innerText = ''; } }

    // (category add via modal removed) - modal now only provides a dropdown to select category

    // Image preview
    document.addEventListener('DOMContentLoaded', function () {
      var imgInput = document.getElementById('productImg');
      if (imgInput) {
        imgInput.addEventListener('change', function (e) {
          const file = e.target.files[0];
          const preview = document.getElementById('imgPreview');
          if (file) {
            const reader = new FileReader();
            reader.onload = function (ev) {
              preview.innerHTML = '<img src="' + ev.target.result + '" alt="Preview" style="max-width:120px;max-height:120px;border-radius:8px;box-shadow:0 2px 8px #0001;">';
            };
            reader.readAsDataURL(file);
          } else {
            preview.innerHTML = '';
          }
        });
      }
    });

    // Bulk premium handlers
    document.getElementById('selectAll').addEventListener('change', function (e) {
      var checked = e.target.checked;
      document.querySelectorAll('.rowCheck').forEach(function (cb) { cb.checked = checked; });
    });

    function getSelectedIds() {
      var ids = [];
      document.querySelectorAll('.rowCheck:checked').forEach(function (cb) { ids.push(cb.value); });
      return ids;
    }

    function doBulk(action) {
      var ids = getSelectedIds();
      if (!ids.length) { alert('No products selected'); return; }
      if (!confirm('Apply action to ' + ids.length + ' products?')) return;
      var fd = new FormData(); fd.append('action', action); ids.forEach(function (id) { fd.append('ids[]', id); });
      fetch('bulk_premium.php', { method: 'POST', body: fd }).then(r => r.json()).then(function (data) {
        if (data.success) { alert('Updated ' + data.affected + ' rows'); location.reload(); } else { alert('Failed: ' + (data.message || '')); }
      }).catch(function (err) { alert('Request failed: ' + err); });
    }

    document.getElementById('markPremiumBtn').addEventListener('click', function () { doBulk('set'); });
    document.getElementById('unmarkPremiumBtn').addEventListener('click', function () { doBulk('unset'); });
  </script>
  <script>
    // Category panel logic
    (function () {
      const panel = document.getElementById('categoryPanel');
      const toggleBtn = document.getElementById('toggleCategoryPanel');
      const closeBtn = document.getElementById('closeCategoryPanel');
      const list = document.getElementById('categoryList');

      function fetchCategories() {
        // Build list from existing `productCategory` select options
        const existing = [];
        document.querySelectorAll('#productCategory option').forEach(function (opt) {
          const v = (opt.value || '').trim();
          if (v) existing.push({ name: v });
        });
        // Clear and render
        list.innerHTML = '';
        existing.forEach(function (c) {
          const el = document.createElement('div'); el.className = 'category-item';
          const img = document.createElement('div'); img.style.width = '44px'; img.style.height = '44px'; img.style.background = '#fff5'; img.style.borderRadius = '8px'; img.style.flex = '0 0 44px';
          const txt = document.createElement('div'); txt.textContent = c.name; txt.style.fontWeight = '600';
          el.appendChild(img); el.appendChild(txt);
          list.appendChild(el);
        });
      }

      toggleBtn.addEventListener('click', function () {
        if (panel.style.display === 'none' || panel.style.display === '') {
          fetchCategories(); panel.style.display = 'flex';
        } else { panel.style.display = 'none'; }
      });
      closeBtn.addEventListener('click', function () { panel.style.display = 'none'; });

      // Add category from panel
      document.getElementById('panelAddCategory').addEventListener('click', function () {
        const name = document.getElementById('panelNewCategory').value.trim();
        const imgFile = document.getElementById('panelNewCategoryImg').files[0];
        if (!name) { alert('Enter a category name'); return; }
        const fd = new FormData(); fd.append('name', name); if (imgFile) fd.append('img', imgFile);
        fetch('add_category.php', { method: 'POST', body: fd }).then(r => r.text()).then(function (txt) {
          try { var data = JSON.parse(txt); } catch (e) { alert('Server error'); return; }
          if (data.success) {
            // append to productCategory select and refresh list
            document.getElementById('productCategory').appendChild(new Option(data.name, data.name));
            document.getElementById('panelNewCategory').value = '';
            fetchCategories();
          } else { alert(data.message || 'Failed to add category'); }
        }).catch(function (err) { alert('Request failed: ' + err); });
      });
    })();
  </script>
</body>

</html>