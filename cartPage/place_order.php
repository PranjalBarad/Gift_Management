<?php
// Ensure the endpoint always returns JSON and doesn't leak HTML errors
session_start();
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
header('Content-Type: application/json');

// Simple logger for server-side errors
$logFile = __DIR__ . '/place_order_error.log';
function log_err($msg)
{
  global $logFile;
  error_log("[" . date('Y-m-d H:i:s') . "] " . $msg . PHP_EOL, 3, $logFile);
}

set_error_handler(function ($severity, $message, $file, $line) {
  throw new ErrorException($message, 0, $severity, $file, $line);
});
set_exception_handler(function ($e) {
  // Log exception
  log_err($e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()]);
  exit;
});

try {
  require_once('../db.php');

  if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
  }

  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  if ($raw === false || $data === null) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
  }

  $cart = $_SESSION['cart'] ?? [];
  if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
  }

  $user = $_SESSION['user'];
  $fullname = trim($data['fullname'] ?? '');
  $phone = trim($data['phone'] ?? '');
  $altphone = trim($data['altphone'] ?? '');
  $address = trim($data['address'] ?? '');
  $gift = isset($data['gift']) ? $data['gift'] : null; // may be array with wrapper/card/accessories/message

  // Basic validation
  if ($fullname === '' || $phone === '' || $address === '') {
    echo json_encode(['success' => false, 'message' => 'Fill required fields']);
    exit;
  }

  // compute totals
  $subtotal = 0;
  foreach ($cart as $item) {
    $subtotal += intval(preg_replace('/[^0-9]/', '', $item['price'])) * $item['qty'];
  }
  $shipping = 0;

  // include gift price if selected
  $gift_total = 0;
  if (!empty($gift) && is_array($gift)) {
    // helper to query price by id
    $getPrice = function($id) use ($conn) {
      $stmt = $conn->prepare('SELECT price FROM gift_items WHERE id=? LIMIT 1');
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $res = $stmt->get_result();
      $row = $res->fetch_assoc();
      return $row ? floatval($row['price']) : 0;
    };
    if (!empty($gift['wrapper'])) {
      $gift_total += $getPrice((int)$gift['wrapper']);
    }
    if (!empty($gift['card'])) {
      $gift_total += $getPrice((int)$gift['card']);
    }
    if (!empty($gift['accessories']) && is_array($gift['accessories'])) {
      foreach ($gift['accessories'] as $aid) {
        $gift_total += $getPrice((int)$aid);
      }
    }
  }

  $total = $subtotal + $gift_total + $shipping;

  // prepare order payload
  $order_payload = ['cart' => $cart];
  if ($gift) {
    $order_payload['gift'] = $gift;
  }

  // Insert order (schema: user_id, phone, altphone, address, order_data, total)
  $stmt = $conn->prepare("INSERT INTO orders (user_id, phone, altphone, address, order_data, total, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
  if (!$stmt) {
    throw new Exception('Prepare failed: ' . ($conn->error ?? 'unknown'));
  }
  $order_data = json_encode($order_payload);
  $stmt->bind_param('issssd', $user['id'], $phone, $altphone, $address, $order_data, $total);
  if (!$stmt->execute()) {
    throw new Exception('DB error: ' . $stmt->error);
  }

  $order_id = $stmt->insert_id;

  // Clear cart and save last order id
  unset($_SESSION['cart']);
  $_SESSION['last_order_id'] = $order_id;

  echo json_encode(['success' => true, 'order_id' => $order_id, 'redirect' => '../orderPage/order.php']);
  exit;

} catch (Throwable $e) {
  // Exception handler above should catch, but keep fallback
  log_err('Uncaught: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()]);
  exit;
}

?>