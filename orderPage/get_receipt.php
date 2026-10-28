<?php
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
  echo json_encode(['success' => false, 'message' => 'Not authenticated']);
  exit;
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$order_id) {
  echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
  exit;
}

$user = $_SESSION['user'];

// Fetch order and ensure it belongs to the logged-in user
$stmt = $conn->prepare("SELECT id, user_id, total, created_at, status, order_data, address, phone FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$order_row = $result->fetch_assoc();

if (!$order_row) {
  echo json_encode(['success' => false, 'message' => 'Order not found']);
  exit;
}

$order_data = json_decode($order_row['order_data'], true) ?? [];

// normalize items and extract gift info if present
$items = [];
$gift_info = null;
if (isset($order_data['cart']) && is_array($order_data['cart'])) {
    $items = $order_data['cart'];
    $gift_info = $order_data['gift'] ?? null;
} elseif (is_array($order_data)) {
    // legacy orders where order_data is array of items
    $items = $order_data;
}

// helper to resolve gift item names/prices
$resolveGift = function($conn, $id) {
    $stmt = $conn->prepare('SELECT id,name,price FROM gift_items WHERE id=? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $r = $stmt->get_result();
    return $r->fetch_assoc() ?: null;
};

$gift_details = null;
$gift_total = 0;
if (!empty($gift_info) && is_array($gift_info)) {
    $gift_details = ['wrapper' => null, 'card' => null, 'accessories' => [], 'message' => $gift_info['message'] ?? ''];
    if (!empty($gift_info['wrapper'])) {
        $g = $resolveGift($conn, (int)$gift_info['wrapper']);
        if ($g) { $gift_details['wrapper'] = $g; $gift_total += floatval($g['price']); }
    }
    if (!empty($gift_info['card'])) {
        $g = $resolveGift($conn, (int)$gift_info['card']);
        if ($g) { $gift_details['card'] = $g; $gift_total += floatval($g['price']); }
    }
    if (!empty($gift_info['accessories']) && is_array($gift_info['accessories'])) {
        foreach ($gift_info['accessories'] as $aid) {
            $g = $resolveGift($conn, (int)$aid);
            if ($g) { $gift_details['accessories'][] = $g; $gift_total += floatval($g['price']); }
        }
    }
}

$response = [
  'success' => true,
  'order' => [
    'id' => (int) $order_row['id'],
    'total' => $order_row['total'],
    'created_at' => date('Y-m-d H:i:s', strtotime($order_row['created_at'])),
    'status' => $order_row['status'] ?? 'Pending',
    'address' => $order_row['address'] ?? 'Address not available',
    'phone' => $order_row['phone'] ?? '',
    'items' => array_map(function($item) {
      return [
        'name' => $item['name'] ?? 'Product',
        'category' => $item['category'] ?? 'Product',
        'qty' => (int) ($item['qty'] ?? 1),
        'price' => '₹' . intval(str_replace('₹', '', $item['price'] ?? 0)),
        'img' => $item['img'] ?? ''
      ];
    }, $items),
    'gift' => $gift_details,
    'gift_charge' => number_format($gift_total, 2)
  ]
];

echo json_encode($response);
?>
