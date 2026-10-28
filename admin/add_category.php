<?php
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json');
include_once __DIR__ . '/../db.php';

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
if ($name === '') {
  echo json_encode(['success' => false, 'message' => 'Category name is required']);
  exit;
}

// Create categories table if it doesn't exist (with img column)
$createSql = "CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `img` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if (!$conn->query($createSql)) {
  echo json_encode(['success' => false, 'message' => 'Failed to ensure categories table: ' . $conn->error]);
  exit;
}

// If the table existed previously without img column, add it now
$colRes = $conn->query("SHOW COLUMNS FROM categories LIKE 'img'");
if (!($colRes && $colRes->num_rows > 0)) {
  $alter = "ALTER TABLE categories ADD COLUMN `img` VARCHAR(255) DEFAULT NULL";
  if (!$conn->query($alter)) {
    echo json_encode(['success' => false, 'message' => 'Failed to add img column: ' . $conn->error]);
    exit;
  }
}

// Handle uploaded image (optional)
$imgPath = null;
if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
  $f = $_FILES['img'];
  // Basic validation: allowed mime types (no size limit enforced here)
  $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif', 'image/webp' => '.webp'];
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $type = finfo_file($finfo, $f['tmp_name']);
  finfo_close($finfo);
  if (!isset($allowed[$type])) {
    echo json_encode(['success' => false, 'message' => 'Unsupported image format']);
    exit;
  }

  $ext = $allowed[$type];
  $dir = __DIR__ . '/../photos/categories';
  if (!is_dir($dir))
    mkdir($dir, 0755, true);
  // unique filename
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
  // store relative path to project root
  $imgPath = 'photos/categories/' . $filename;
}

// Try insert (ignore duplicate name)
$stmt = $conn->prepare('INSERT IGNORE INTO categories (name, img) VALUES (?, ?)');
if (!$stmt) {
  echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
  exit;
}
$stmt->bind_param('ss', $name, $imgPath);
if (!$stmt->execute()) {
  echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
  exit;
}

$id = $conn->insert_id;
if ($id == 0) {
  // existing row: fetch id and img
  $sel = $conn->prepare('SELECT id, img FROM categories WHERE name = ? LIMIT 1');
  $sel->bind_param('s', $name);
  $sel->execute();
  $res = $sel->get_result();
  $row = $res->fetch_assoc();
  $id = $row ? (int) $row['id'] : 0;
  $existingImg = $row ? $row['img'] : null;
  // if we uploaded a new image but DB had no image, update it; otherwise delete uploaded to avoid orphan
  if ($imgPath) {
    if (empty($existingImg) && $id) {
      $up = $conn->prepare('UPDATE categories SET img = ? WHERE id = ?');
      if ($up) {
        $up->bind_param('si', $imgPath, $id);
        $up->execute();
      }
      $dbImg = $imgPath;
    } else {
      // remove uploaded file because DB already has an image
      @unlink(__DIR__ . '/../' . $imgPath);
      $dbImg = $existingImg;
    }
  } else {
    $dbImg = $existingImg;
  }
} else {
  // newly inserted
  $dbImg = $imgPath;
}

echo json_encode(['success' => true, 'id' => (int) $id, 'name' => $name, 'img' => $dbImg]);

?>