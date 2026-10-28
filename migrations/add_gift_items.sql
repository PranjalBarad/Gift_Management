-- migration to add gift_items table

CREATE TABLE IF NOT EXISTS `gift_items` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` enum('wrapper','card','accessory') NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `img` varchar(512) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
