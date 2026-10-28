<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

// Fetch all users and their order count
$sql = "SELECT u.id, u.name, u.email, COUNT(o.id) as order_count
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id
        GROUP BY u.id, u.name, u.email
        ORDER BY u.id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customers - Buze Co Admin</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🎁 Buze Co</h2>
    <a href="index.php">📊 Dashboard</a>
    <a href="products.php">📦 Products</a>
    <a href="gift_items.php">🎀 Gift Wrapper</a>
    <a href="orders.php">🛒 Orders</a>
    <a href="customers.php" class="active">👥 Customers</a>
    <a href="contactus.php">📨 Contact Us</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Customers</h1>
      <div class="profile">
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="18" cy="18" r="18" fill="#E0E0E0" />
          <circle cx="18" cy="14" r="7" fill="#BDBDBD" />
          <ellipse cx="18" cy="27" rx="11" ry="6" fill="#BDBDBD" />
        </svg>
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
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td><?php echo htmlspecialchars($row['email']); ?></td>
          <td><?php echo $row['order_count']; ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>

</html>