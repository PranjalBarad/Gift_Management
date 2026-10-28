<?php
session_start();
header('Content-Type: application/json');
require_once('../db.php');

// make sure table exists (silent fail)
$conn->query("CREATE TABLE IF NOT EXISTS `gift_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('wrapper','card','accessory') NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `img` VARCHAR(512) DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `color` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$options = ['wrapper' => [], 'card' => [], 'accessory' => []];
$result = $conn->query("SELECT * FROM gift_items ORDER BY type, name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $options[$row['type']][] = $row;
    }
    $result->free();
}

echo json_encode(['success' => true, 'options' => $options]);
