<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Buze Co Admin</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>
  <?php
  // Require admin authentication
  require_once __DIR__ . '/auth.php';
  // include DB and fetch summary counts + recent orders
  include_once __DIR__ . '/../db.php';

  // Fetch summary counts
  $totalOrders = 0;
  $totalProducts = 0;
  $totalCustomers = 0;
  try {
    $r = $conn->query("SELECT COUNT(*) AS cnt FROM orders");
    if ($r) {
      $totalOrders = (int) $r->fetch_assoc()['cnt'];
      $r->free();
    }
    $r = $conn->query("SELECT COUNT(*) AS cnt FROM products");
    if ($r) {
      $totalProducts = (int) $r->fetch_assoc()['cnt'];
      $r->free();
    }
    $r = $conn->query("SELECT COUNT(*) AS cnt FROM users");
    if ($r) {
      $totalCustomers = (int) $r->fetch_assoc()['cnt'];
      $r->free();
    }
  } catch (Exception $e) {
    // keep defaults on error
  }

  // Fetch recent orders (limit 6)
  $recentOrders = [];
  $ordRes = $conn->query("SELECT o.id, o.total, o.created_at, o.address, o.order_data, o.status, u.name, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 6");
  if ($ordRes) {
    while ($row = $ordRes->fetch_assoc()) {
      $recentOrders[] = $row;
    }
    $ordRes->free();
  }

  ?>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🎁 Buze Co</h2>
    <a href="index.php" class="active">📊 Dashboard</a>
    <a href="products.php">📦 Products</a>
    <a href="gift_items.php">🎀 Gift Wrapper</a>
    <a href="orders.php">🛒 Orders</a>
    <a href="customers.php">👥 Customers</a>
    <!-- <a href="notifications.php">🔔 Notifications</a> -->
    <a href="contactus.php">📨 Contact Us</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Dashboard</h1>
      <div class="profile">
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="18" cy="18" r="18" fill="#E0E0E0" />
          <circle cx="18" cy="14" r="7" fill="#BDBDBD" />
          <ellipse cx="18" cy="27" rx="11" ry="6" fill="#BDBDBD" />
        </svg>
        <span>Admin</span>
      </div>
    </div>

    <div class="dashboard">
      <div class="card">
        <h3>Total Orders</h3>
        <p><?= (int) $totalOrders ?></p>
      </div>
      <div class="card">
        <h3>Total Products</h3>
        <p><?= (int) $totalProducts ?></p>
      </div>
      <div class="card">
        <h3>Customers</h3>
        <p><?= (int) $totalCustomers ?></p>
      </div>
    </div>
    <!-- Order Table Section -->
    <div class="orders-table" style="margin-top: 40px;">
      <h2>Recent Orders</h2>
      <table class="table">
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Date</th>
          <th>Delivery Address</th>
          <th>Status</th>
          <th>Total</th>
        </tr>
        <?php if (empty($recentOrders)): ?>
          <tr>
            <td colspan="5">No recent orders.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($recentOrders as $or):
            $pList = json_decode($or['order_data'], true);
            ?>
            <tr data-order-id="<?= (int) $or['id'] ?>">
              <td><?= (int) $or['id'] ?></td>
              <td><?= htmlspecialchars($or['name'] ?? $or['email'] ?? 'Guest') ?></td>
              <td><?= htmlspecialchars($or['created_at']) ?></td>
              <td class="address" title="<?= htmlspecialchars($or['address']) ?>">
                <?= nl2br(htmlspecialchars($or['address'])) ?>
              </td>
              <td>
                <select class="order-status" onchange="updateOrderStatus(this)" data-order-id="<?= (int) $or['id'] ?>">
                  <?php
                  $cur = $or['status'] ?? 'Pending';
                  $opts = ['Pending', 'Shipped', 'Delivered'];
                  foreach ($opts as $op) {
                    $sel = ($op === $cur) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($op) . "\" $sel>" . htmlspecialchars($op) . "</option>";
                  }
                  ?>
                </select>
                <?php
                // Server-rendered status badge so it persists after refresh
                $colorMap = ['Pending' => '#f59e0b', 'Shipped' => '#3b82f6', 'Delivered' => '#10b981'];
                $badgeText = $cur;
                $badgeColor = $colorMap[$cur] ?? '#ddd';
                ?>
                <span class="status-badge"
                  style="margin-left:8px;padding:4px 8px;border-radius:6px;font-weight:600;background:<?= htmlspecialchars($badgeColor) ?>;color:#fff;"><?= htmlspecialchars($badgeText) ?></span>
              </td>
              <td>₹<?= htmlspecialchars(number_format($or['total'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </table>
    </div>
  </div>

  <script>
    async function updateOrderStatus(selectElem) {
      const newStatus = selectElem.value;
      const orderId = selectElem.getAttribute('data-order-id') || selectElem.closest('tr')?.getAttribute('data-order-id');
      if (!orderId) { alert('Unable to determine order id'); return; }

      // create small feedback element
      let fb = selectElem.nextElementSibling;
      if (!fb || !fb.classList || !fb.classList.contains('status-feedback')) {
        fb = document.createElement('span');
        fb.className = 'status-feedback';
        fb.style.marginLeft = '8px';
        selectElem.parentNode.appendChild(fb);
      }
      fb.textContent = 'Saving...';

      try {
        const fd = new FormData();
        fd.append('order_id', orderId);
        fd.append('status', newStatus);
        const res = await fetch('update_order_status.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data && data.success) {
          // Update or create a visual status badge in the row
          const row = selectElem.closest('tr');
          if (row) {
            let badge = row.querySelector('.status-badge');
            if (!badge) {
              badge = document.createElement('span');
              badge.className = 'status-badge';
              badge.style.marginLeft = '8px';
              badge.style.padding = '4px 8px';
              badge.style.borderRadius = '6px';
              badge.style.fontWeight = '600';
              selectElem.parentNode.insertBefore(badge, fb);
            }
            const colorMap = { 'Pending': '#f59e0b', 'Shipped': '#3b82f6', 'Delivered': '#10b981' };
            badge.textContent = data.status || newStatus;
            badge.style.background = colorMap[data.status || newStatus] || '#ddd';
            badge.style.color = '#fff';
          }
          fb.textContent = 'Saved'; fb.style.color = 'green'; setTimeout(() => { fb.textContent = ''; }, 2000);
        } else {
          fb.textContent = data.error || 'Save failed';
          fb.style.color = 'red';
        }
      } catch (err) {
        fb.textContent = 'Request failed';
        fb.style.color = 'red';
      }
    }
  </script>
</body>

</html>