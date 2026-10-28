<?php
session_start();
require_once '../../project/db.php';
require_once '../headerPage/header.php';

if (!isset($_SESSION['user'])) {
  header('Location: log.php');
  exit;
}

$user = $_SESSION['user'];

$stmt = $conn->prepare("SELECT id, total, created_at, status FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
while ($r = $result->fetch_assoc()) {
  $orders[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>My Orders</title>
  <link rel="stylesheet" href="../logIn/profile.css">
</head>

<body>
  <div style="max-width:900px;margin:40px auto;">
    <h2>My Orders</h2>
    <?php if (empty($orders)): ?>
      <p>You have no orders yet.</p>
      <a href="../shopPage/shop2.php">Start Shopping</a>
    <?php else: ?>
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr style="text-align:left;border-bottom:1px solid #ddd;">
            <th>Order ID</th>
            <th>Date</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr style="border-bottom:1px solid #f1f1f1;">
              <td>#<?php echo $o['id']; ?></td>
              <td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
              <td>₹<?php echo $o['total']; ?></td>
              <td>
                <?php
                $colorMap = ['Pending' => '#f59e0b', 'Shipped' => '#3b82f6', 'Delivered' => '#10b981'];
                $st = $o['status'] ?? 'Pending';
                $bg = $colorMap[$st] ?? '#777';
                ?>
                <span
                  style="display:inline-block;padding:6px 10px;border-radius:6px;background:<?= htmlspecialchars($bg) ?>;color:#fff;font-weight:600"><?php echo htmlspecialchars($st); ?></span>
              </td>
              <td><a href="../orderPage/order.php?id=<?php echo $o['id']; ?>">View</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>

</html>
<?php require_once('../footerPage/footer.php'); ?>