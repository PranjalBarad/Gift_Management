<?php
require_once __DIR__ . '/auth.php';
// This file handles AJAX status updates for orders
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  include_once __DIR__ . '/../db.php';
  header('Content-Type: application/json');

  // simple file logger to capture DB errors during development
  $logFile = __DIR__ . '/update_order_status.log';
  function log_msg($msg)
  {
    global $logFile;
    error_log("[" . date('Y-m-d H:i:s') . "] " . $msg . PHP_EOL, 3, $logFile);
  }

  $orderId = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
  $newStatus = isset($_POST['status']) ? trim($_POST['status']) : '';

  if ($orderId <= 0 || $newStatus === '') {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    log_msg("Invalid input: order_id={$orderId} status=" . var_export($newStatus, true));
    exit;
  }

  // Allowed statuses - adjust as needed
  $allowed = ['Pending', 'Shipped', 'Delivered'];
  if (!in_array($newStatus, $allowed, true)) {
    echo json_encode(['success' => false, 'error' => 'Unknown status']);
    log_msg("Unknown status attempted for order {$orderId}: {$newStatus}");
    exit;
  }

  try {
    $stmt = $conn->prepare('UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?');
    if (!$stmt) {
      $err = $conn->error ?? 'prepare failed';
      log_msg("Prepare failed: {$err}");
      echo json_encode(['success' => false, 'error' => 'Database prepare failed']);
      exit;
    }
    $stmt->bind_param('si', $newStatus, $orderId);
    if ($stmt->execute()) {
      echo json_encode(['success' => true, 'status' => $newStatus]);
    } else {
      $err = $stmt->error;
      log_msg("Execute failed for order {$orderId}: {$err}");
      echo json_encode(['success' => false, 'error' => 'Database execute failed']);
    }
    $stmt->close();
  } catch (Throwable $e) {
    log_msg('Exception: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
  }
  exit;
}
