<?php
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json');
include_once __DIR__ . '/../db.php';

$id = 0;
if (isset($_POST['id']))
  $id = (int) $_POST['id'];
elseif (isset($_GET['id']))
  $id = (int) $_GET['id'];

if ($id <= 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid id']);
  exit;
}

// find product and its image (if any)
$sel = $conn->prepare('SELECT img FROM products WHERE id = ? LIMIT 1');
if (!$sel) {
  echo json_encode(['success' => false, 'message' => $conn->error]);
  exit;
}
$sel->bind_param('i', $id);
$sel->execute();
$res = $sel->get_result();
$row = $res->fetch_assoc();
if (!$row) {
  echo json_encode(['success' => false, 'message' => 'Product not found']);
  exit;
}
$img = isset($row['img']) ? $row['img'] : null;

$del = $conn->prepare('DELETE FROM products WHERE id = ?');
if (!$del) {
  echo json_encode(['success' => false, 'message' => $conn->error]);
  exit;
}
$del->bind_param('i', $id);
if (!$del->execute()) {
  echo json_encode(['success' => false, 'message' => $del->error]);
  exit;
}

$affected = $del->affected_rows;
// remove image file if present and path looks safe
if ($affected > 0 && !empty($img)) {
  $path = __DIR__ . '/../' . ltrim($img, '/\\');
  if (is_file($path))
    @unlink($path);
}

echo json_encode(['success' => true, 'affected' => $affected]);

?>