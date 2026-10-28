<?php
session_start();
header('Content-Type: application/json');
require_once('../db.php');

$user = $_SESSION['user'] ?? null;
$cart = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cart as $item) {
  $subtotal += intval(preg_replace('/[^0-9]/', '', $item['price'])) * $item['qty'];
}
$shipping = 0;
$total = $subtotal + $shipping;

echo json_encode([
  'success' => true,
  'user' => $user ? ['id' => $user['id'], 'name' => $user['name'], 'phone' => $user['phone'] ?? ''] : null,
  'cart' => array_values($cart),
  'subtotal' => $subtotal,
  'shipping' => $shipping,
  'total' => $total
]);

?>