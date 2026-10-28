<?php
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json');
include_once __DIR__ . '/../db.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid id']);
  exit;
}
$stmt = $conn->prepare('SELECT id, name, category, description, includes, price, img, IFNULL(is_premium,0) as is_premium FROM products WHERE id = ? LIMIT 1');
if (!$stmt) {
  echo json_encode(['success' => false, 'message' => $conn->error]);
  exit;
}
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) {
  echo json_encode(['success' => false, 'message' => 'Not found']);
  exit;
}
echo json_encode(['success' => true, 'product' => $row]);
?>