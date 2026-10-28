<?php
// admin/contactus.php
// Admin Contact Us Page

// Require admin authentication
require_once __DIR__ . '/auth.php';
require_once(__DIR__ . '/../db.php');

// Fetch contact submissions
$submissions = [];
$res = $conn->query("SELECT id, name, email, message, created_at FROM contact_messages ORDER BY created_at DESC");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $submissions[] = $row;
  }
  $res->free();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Buze Co Admin</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .contact-table {
      width: 100%;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
      background: var(--white);
      margin-top: 20px;
    }

    .contact-table th,
    .contact-table td {
      border-bottom: 1px solid #e5e7eb;
      padding: 14px 12px;
      text-align: left;
    }

    .contact-table th {
      background: #f1f5f9;
      color: var(--primary);
      font-size: 15px;
      font-weight: 600;
    }

    .contact-table tr:nth-child(even) {
      background: #f6f8fa;
    }

    .contact-table tr:hover {
      background: #e0e7ef;
      transition: background 0.2s;
    }

    .contact-card {
      background: var(--white);
      border-radius: 12px;
      padding: 24px 20px 32px 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
      margin-top: 30px;
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
    <a href="orders.php">🛒 Orders</a>
    <a href="customers.php">👥 Customers</a>
    <a href="contactus.php" class="active">📨 Contact Us</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Contact Us Submissions</h1>
      <div class="profile">
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="18" cy="18" r="18" fill="#E0E0E0" />
          <circle cx="18" cy="14" r="7" fill="#BDBDBD" />
          <ellipse cx="18" cy="27" rx="11" ry="6" fill="#BDBDBD" />
        </svg>
        <span>Admin</span>
      </div>
    </div>

    <div class="contact-card">
      <table class="contact-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($submissions)): ?>
            <?php foreach ($submissions as $s): ?>
              <tr>
                <td><?php echo htmlspecialchars($s['id']); ?></td>
                <td><?php echo htmlspecialchars($s['name']); ?></td>
                <td><?php echo htmlspecialchars($s['email']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($s['message'])); ?></td>
                <td><?php echo htmlspecialchars(substr($s['created_at'], 0, 10)); ?></td>
                <td><button class="btn mark-read-btn" onclick="markAsRead(this)">Mark as Read</button></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">No contact submissions found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
<script>
  function markAsRead(btn) {
    btn.textContent = 'Read';
    btn.disabled = true;
    btn.classList.add('btn-success');
  }
</script>

</html>