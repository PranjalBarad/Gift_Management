<?php
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json');
include_once __DIR__ . '/../db.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';
$ids = isset($_POST['ids']) && is_array($_POST['ids']) ? $_POST['ids'] : [];
if (!in_array($action, ['set', 'unset'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid action']);
  exit;
}
if (empty($ids)) {
  echo json_encode(['success' => false, 'message' => 'No ids provided']);
  exit;
}

// Ensure is_premium column exists
$colRes = $conn->query("SHOW COLUMNS FROM products LIKE 'is_premium'");
if (!$colRes || $colRes->num_rows === 0) {
  $alter = $conn->query("ALTER TABLE products ADD COLUMN is_premium TINYINT(1) NOT NULL DEFAULT 0");
  if (!$alter) {
    echo json_encode(['success' => false, 'message' => 'Failed to add column: ' . $conn->error]);
    exit;
  }
}

$val = ($action === 'set') ? 1 : 0;
// Prepare IN clause safely
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));
$sql = "UPDATE products SET is_premium = ? WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  echo json_encode(['success' => false, 'message' => $conn->error]);
  exit;
}

$params = array_merge([$val], $ids);
$bind_names[] = str_repeat('i', count($params));

// bind_param with dynamic params
$types = str_repeat('i', count($params));
$bind_params = [];
foreach ($params as $k => $v) {
  $bind_params[$k] = $v;
}

$refarr = [];
foreach ($bind_params as $key => $value) {
  $refarr[$key] = &$bind_params[$key];
}
array_unshift($refarr, $types);
call_user_func_array([$stmt, 'bind_param'], $refarr);

if (!$stmt->execute()) {
  echo json_encode(['success' => false, 'message' => $stmt->error]);
  exit;
}
echo json_encode(['success' => true, 'affected' => $stmt->affected_rows]);

?>