<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders - Wow Box Admin</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🎁 Wow Box</h2>
    <a href="index.php">📊 Dashboard</a>
    <a href="products.php">📦 Products</a>
    <a href="orders.php" class="active">🛒 Orders</a>
    <a href="customers.php">👥 Customers</a>
    <a href="notifications.php">🔔 Notifications</a>
    <a href="#">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Orders</h1>
      <div class="profile">
        <img src="https://i.pravatar.cc/36" alt="Admin">
        <span>Admin</span>
      </div>
    </div>

    <table class="table">
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Product</th>
        <th>Status</th>
        <th>Date</th>
      </tr>
      <tr>
        <td>#1025</td>
        <td>Rohan Mehta</td>
        <td>Festive Hamper</td>
        <td>Pending</td>
        <td>17 Sep 2025</td>
      </tr>
      <tr>
        <td>#1024</td>
        <td>Sneha Patel</td>
        <td>Chocolate Box</td>
        <td>Shipped</td>
        <td>16 Sep 2025</td>
      </tr>
    </table>
  </div>
</body>
</html>
