<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications - Buze Co Admin</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>
  <?php require_once __DIR__ . '/auth.php'; ?>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🎁 Buze Co</h2>
    <a href="index.php">📊 Dashboard</a>
    <a href="products.php">📦 Products</a>
    <a href="gift_items.php">🎀 Gift Wrapper</a>
    <a href="orders.php">🛒 Orders</a>
    <a href="customers.php">👥 Customers</a>
    <a href="notifications.php" class="active">🔔 Notifications</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Notifications</h1>
      <div class="profile">
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="18" cy="18" r="18" fill="#E0E0E0" />
          <circle cx="18" cy="14" r="7" fill="#BDBDBD" />
          <ellipse cx="18" cy="27" rx="11" ry="6" fill="#BDBDBD" />
        </svg>
        <span>Admin</span>
      </div>
    </div>

    <div class="notifications">
      <div class="notification new">
        <p><strong>New Order</strong> from <b>Rohan Mehta</b> (Order #1025)</p>
        <span>2 mins ago</span>
      </div>
      <div class="notification">
        <p><strong>Low Stock:</strong> Festive Hamper only 5 left</p>
        <span>1 hour ago</span>
      </div>
      <div class="notification">
        <p><strong>Customer Message:</strong> Sneha Patel asked about delivery</p>
        <span>3 hours ago</span>
      </div>
    </div>
  </div>
</body>

</html>