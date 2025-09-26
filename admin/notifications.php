<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications - Wow Box Admin</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🎁 Wow Box</h2>
    <a href="index.php">📊 Dashboard</a>
    <a href="products.php">📦 Products</a>
    <a href="orders.php">🛒 Orders</a>
    <a href="customers.php">👥 Customers</a>
    <a href="notifications.php" class="active">🔔 Notifications</a>
    <a href="#">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Notifications</h1>
      <div class="profile">
        <img src="https://i.pravatar.cc/36" alt="Admin">
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
