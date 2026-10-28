<?php
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json');
include_once __DIR__ . '/../db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}
$id = intval($data['id']);

$stmt = $conn->prepare('SELECT img FROM gift_items WHERE id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

$del = $conn->prepare('DELETE FROM gift_items WHERE id=?');
$del->bind_param('i', $id);
if ($del->execute()) {
    // remove image file if any
    if ($row && !empty($row['img'])) {
        @unlink(__DIR__ . '/../' . $row['img']);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'DB delete failed']);
}

?>
