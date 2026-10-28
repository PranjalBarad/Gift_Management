<?php
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json');
include_once __DIR__ . '/../db.php';

// ensure table exists (no color column)
$createSql = "CREATE TABLE IF NOT EXISTS `gift_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('wrapper','card','accessory') NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `img` VARCHAR(512) DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if (! $conn->query($createSql)) {
    echo json_encode(['success' => false, 'message' => 'Unable to ensure gift_items table: ' . $conn->error]);
    exit;
}

$type = $_POST['type'] ?? '';
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;

if (!in_array($type, ['wrapper','card','accessory'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid type']);
    exit;
}
if ($name === '') {
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    exit;
}

// handle optional image upload
$imgPath = null;
if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $f = $_FILES['img'];
    $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif', 'image/webp' => '.webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $typeM = finfo_file($finfo, $f['tmp_name']);
    finfo_close($finfo);
    if (!isset($allowed[$typeM])) {
        echo json_encode(['success' => false, 'message' => 'Unsupported image format']);
        exit;
    }
    $ext = $allowed[$typeM];
    $dir = __DIR__ . '/../photos/gift';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    try {
        $rnd = bin2hex(random_bytes(6));
    } catch (Exception $e) {
        $rnd = uniqid();
    }
    $filename = time() . '_' . $rnd . $ext;
    $dest = $dir . '/' . $filename;
    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
        exit;
    }
    $imgPath = 'photos/gift/' . $filename;
}

$stmt = $conn->prepare('INSERT INTO gift_items (type,name,description,img,price) VALUES (?,?,?,?,?)');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param('ssssd', $type, $name, $description, $imgPath, $price);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
    exit;
}
$id = $stmt->insert_id;
echo json_encode(['success' => true, 'id' => $id, 'type' => $type]);
