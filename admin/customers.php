<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customers - Wow Box Admin</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🎁 Wow Box</h2>
    <a href="index.php">📊 Dashboard</a>
    <a href="products.php">📦 Products</a>
    <a href="orders.php">🛒 Orders</a>
    <a href="customers.php" class="active">👥 Customers</a>
    <a href="notifications.php">🔔 Notifications</a>
    <a href="#">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Customers</h1>
      <div class="profile">
        <img src="https://i.pravatar.cc/36" alt="Admin">
        <span>Admin</span>
      </div>
    </div>

    <table class="table">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Orders</th>
      </tr>
      <tr>
        <td>1</td>
        <td>Rohan Mehta</td>
        <td>rohan@example.com</td>
        <td>5</td>
      </tr>
      <tr>
        <td>2</td>
        <td>Sneha Patel</td>
        <td>sneha@example.com</td>
        <td>3</td>
      </tr>
    </table>
  </div>
</body>
</html>
