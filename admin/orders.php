<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
// require_once('../headerPage/header.php');

// Optional: Only allow admin access
// if (!isset($_SESSION['user']) || $_SESSION['user']['email'] !== 'yesha@gmail.com') {
//     header('Location: ../logIn/log.php');
//     exit;
// }

$stmt = $conn->query("SELECT o.*, u.email, u.name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders - Buze Co Admin</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }

    th,
    td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    th {
      background: #f5f5f5;
    }

    .products-list {
      font-size: 0.95em;
      color: #555;
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🎁 Buze Co</h2>
    <a href="index.php">📊 Dashboard</a>
    <a href="products.php">📦 Products</a>
    <a href="gift_items.php">🎀 Gift Wrapper</a>
    <a href="orders.php" class="active">🛒 Orders</a>
    <a href="customers.php">👥 Customers</a>
    <a href="contactus.php">📨 Contact Us</a>
    <!-- <a href="notifications.php">🔔 Notifications</a> -->
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Orders</h1>
      <div class="profile">
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="18" cy="18" r="18" fill="#E0E0E0" />
          <circle cx="18" cy="14" r="7" fill="#BDBDBD" />
          <ellipse cx="18" cy="27" rx="11" ry="6" fill="#BDBDBD" />
        </svg>
        <span>Admin</span>
      </div>
    </div>

    <h1>All Orders</h1>
    <table>
      <tr>
        <th>ID</th>
        <th>User</th>
        <th>Contact</th>
        <th>Address</th>
        <th>Products</th>
        <th>Status</th>
        <th>Total</th>
        <th>Date</th>
      </tr>
      <?php while ($order = $stmt->fetch_assoc()):
        $products = json_decode($order['order_data'], true);
        ?>
        <tr data-order-id="<?php echo $order['id']; ?>">
          <td><?php echo $order['id']; ?></td>
          <td>
            <?php echo htmlspecialchars($order['name']); ?><br>

          </td>
          <td>
            <?php echo htmlspecialchars($order['phone']); ?>
            <?php if ($order['altphone']): ?><br><small>Alt:
                <?php echo htmlspecialchars($order['altphone']); ?></small><?php endif; ?>
          </td>
          <td class="address" title="<?php echo htmlspecialchars($order['address']); ?>">
            <?php echo nl2br(htmlspecialchars($order['address'])); ?>
          </td>
          <td class="products-list">
            <ul>
              <?php if (is_array($products)): foreach ($products as $item): 
                  // robust name detection (different order formats)
                  $iname = '(unknown)';
                  $iqty = 0;
                  if (is_string($item)) {
                    $iname = $item;
                    $iqty = 1;
                  } elseif (is_array($item)) {
                    if (!empty($item['name'])) $iname = $item['name'];
                    elseif (!empty($item['title'])) $iname = $item['title'];
                    elseif (!empty($item['product_name'])) $iname = $item['product_name'];
                    elseif (!empty($item['product']) && is_array($item['product']) && !empty($item['product']['name'])) $iname = $item['product']['name'];
                    elseif (!empty($item['product']) && is_string($item['product'])) $iname = $item['product'];

                    $iqty = isset($item['qty']) ? (int)$item['qty'] : (isset($item['quantity']) ? (int)$item['quantity'] : (isset($item['count']) ? (int)$item['count'] : (isset($item['q']) ? (int)$item['q'] : 0)));
                    // fallback: try scanning nested values for a readable name
                    if ($iname === '(unknown)') {
                      if (is_array($item)) {
                        foreach ($item as $k => $v) {
                          if (is_string($v) && trim($v) !== '') { $iname = $v; break; }
                          if (is_array($v) && !empty($v['name'])) { $iname = $v['name']; break; }
                        }
                      }
                      if ($iname === '(unknown)') {
                        $iname = substr(json_encode($item), 0, 60) . '...';
                      }
                    }
                  }
              ?>
                <li style="list-style-type: none;"><?php echo htmlspecialchars($iname); ?> ×
                  <?php echo htmlspecialchars($iqty); ?>
                </li>
              <?php endforeach; else: ?>
                <li style="list-style-type:none;">[no products data]</li>
              <?php endif; ?>
            </ul>
          </td>
          <td>
            <select class="order-status" onchange="updateOrderStatus(this)" data-order-id="<?php echo $order['id']; ?>">
              <?php
              $cur = $order['status'] ?? 'Pending';
              $opts = ['Pending', 'Shipped', 'Delivered'];
              foreach ($opts as $op) {
                $sel = ($op === $cur) ? 'selected' : '';
                echo "<option value=\"" . htmlspecialchars($op) . "\" $sel>" . htmlspecialchars($op) . "</option>";
              }
              ?>
            </select>
            <?php
            $colorMap = ['Pending' => '#f59e0b', 'Shipped' => '#3b82f6', 'Delivered' => '#10b981'];
            $badgeText = $cur;
            $badgeColor = $colorMap[$cur] ?? '#ddd';
            ?>
            <span class="status-badge"
              style="margin-left:8px;padding:4px 8px;border-radius:6px;font-weight:600;background:<?= htmlspecialchars($badgeColor) ?>;color:#fff;"><?php echo htmlspecialchars($badgeText); ?></span>
          </td>
          <td>₹<?php echo $order['total']; ?></td>
          <td><?php echo $order['created_at']; ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
  <script>
    async function updateOrderStatus(selectElem) {
      const newStatus = selectElem.value;
      const orderId = selectElem.getAttribute('data-order-id') || selectElem.closest('tr')?.getAttribute('data-order-id');
      if (!orderId) { alert('Unable to determine order id'); return; }

      let fb = selectElem.nextElementSibling;
      if (!fb || !fb.classList || !fb.classList.contains('status-feedback')) {
        fb = document.createElement('span'); fb.className = 'status-feedback'; fb.style.marginLeft = '8px'; selectElem.parentNode.appendChild(fb);
      }
      fb.textContent = 'Saving...';

      try {
        const fd = new FormData(); fd.append('order_id', orderId); fd.append('status', newStatus);
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
        } else { fb.textContent = data.error || 'Save failed'; fb.style.color = 'red'; }
      } catch (err) { fb.textContent = 'Request failed'; fb.style.color = 'red'; }
    }
  </script>
</body>

</html>