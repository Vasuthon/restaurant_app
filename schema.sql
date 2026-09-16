SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `restaurant_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `restaurant_db`;

-- 1. USERS
CREATE TABLE IF NOT EXISTS `USERS` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL,
  `created_by` VARCHAR(36) NULL,
  `updated_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`updated_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. TABLES
CREATE TABLE IF NOT EXISTS `TABLES` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `table_number` VARCHAR(20) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'available',
  `created_by` VARCHAR(36) NULL,
  `updated_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`updated_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABLE_SESSIONS
CREATE TABLE IF NOT EXISTS `TABLE_SESSIONS` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `table_id` VARCHAR(36) NOT NULL,
  `qr_code_token` VARCHAR(255) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'open',
  `package_name` VARCHAR(100) NULL,
  `package_price` INT NOT NULL DEFAULT 0,
  `adults` INT NOT NULL DEFAULT 1,
  `children` INT NOT NULL DEFAULT 0,
  `duration_minutes` INT NOT NULL DEFAULT 120,
  `opened_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `closed_at` TIMESTAMP NULL,
  `created_by` VARCHAR(36) NULL,
  `updated_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`table_id`) REFERENCES `TABLES`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`updated_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. CATEGORIES
CREATE TABLE IF NOT EXISTS `CATEGORIES` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(500) NULL,
  `created_by` VARCHAR(36) NULL,
  `updated_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`updated_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. MENU_ITEMS
CREATE TABLE IF NOT EXISTS `MENU_ITEMS` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `category_id` VARCHAR(36) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` VARCHAR(500) NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `image_url` VARCHAR(500) NULL,
  `emoji` VARCHAR(10) NULL,
  `min_package` INT NOT NULL DEFAULT 0,
  `is_available` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` VARCHAR(36) NULL,
  `updated_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `CATEGORIES`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`updated_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. CARTS
CREATE TABLE IF NOT EXISTS `CARTS` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `session_id` VARCHAR(36) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'active',
  `created_by` VARCHAR(36) NULL,
  `updated_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`session_id`) REFERENCES `TABLE_SESSIONS`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`updated_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. CART_ITEMS
CREATE TABLE IF NOT EXISTS `CART_ITEMS` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `cart_id` VARCHAR(36) NOT NULL,
  `menu_item_id` VARCHAR(36) NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1,
  `note` VARCHAR(500) NULL,
  `created_by` VARCHAR(36) NULL,
  `updated_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`cart_id`) REFERENCES `CARTS`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`menu_item_id`) REFERENCES `MENU_ITEMS`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`updated_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. ORDERS
CREATE TABLE IF NOT EXISTS `ORDERS` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `session_id` VARCHAR(36) NOT NULL,
  `cart_id` VARCHAR(36) NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `note` VARCHAR(500) NULL,
  `created_by` VARCHAR(36) NULL,
  `updated_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`session_id`) REFERENCES `TABLE_SESSIONS`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cart_id`) REFERENCES `CARTS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`updated_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. ORDER_ITEMS
CREATE TABLE IF NOT EXISTS `ORDER_ITEMS` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `order_id` VARCHAR(36) NOT NULL,
  `menu_item_id` VARCHAR(36) NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL,
  `price_at_order` DECIMAL(10,2) NOT NULL,
  `note` VARCHAR(500) NULL,
  `item_status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_by` VARCHAR(36) NULL,
  `updated_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `ORDERS`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`menu_item_id`) REFERENCES `MENU_ITEMS`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`updated_by`) REFERENCES `USERS`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. SERVICE_CALLS (คำขอเรียกพนักงานจากลูกค้า)
CREATE TABLE IF NOT EXISTS `SERVICE_CALLS` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY,
  `table_id` VARCHAR(36) NOT NULL,
  `session_id` VARCHAR(36) NOT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`table_id`) REFERENCES `TABLES`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`session_id`) REFERENCES `TABLE_SESSIONS`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตัวอย่างข้อมูลเริ่มต้น (Initial Data)
INSERT INTO `CATEGORIES` (`id`, `name`, `description`) VALUES
('cat-1', 'เนื้อสัตว์', 'เนื้อสไลซ์และเนื้อพรีเมียมคัดสรรอย่างดี'),
('cat-2', 'ซีฟู้ด', 'อาหารทะเลสดใหม่ทุกวัน'),
('cat-3', 'ผัก/เห็ด', 'ผักสดและเห็ดหลากชนิด'),
('cat-4', 'ลูกชิ้น/ของทอด', 'ลูกชิ้นและของทอดยอดนิยม'),
('cat-5', 'ซุป/น้ำจิ้ม', 'น้ำซุปหมาล่าและน้ำจิ้มสูตรพิเศษ');

INSERT INTO `MENU_ITEMS` (`id`, `category_id`, `name`, `price`, `image_url`, `emoji`, `min_package`, `is_available`) VALUES
('item-1', 'cat-1', 'เนื้อริบอายออสเตรเลีย', 0.00, 'https://images.unsplash.com/photo-1544025162-d76694265947?w=400', '🥩', 399, 1),
('item-2', 'cat-1', 'เนื้อวากิว A5 สไลซ์', 0.00, 'https://images.unsplash.com/photo-1558030006-450675393462?w=400', '🥩', 499, 1),
('item-3', 'cat-1', 'หมูสามชั้นสไลซ์', 0.00, 'https://images.unsplash.com/photo-1608039829572-78524f79c4c7?w=400', '🥓', 299, 1),
('item-4', 'cat-1', 'สันคอหมูคุโรบุตะ', 0.00, 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=400', '🥩', 399, 1),
('item-5', 'cat-2', 'กุ้งแม่น้ำสด', 0.00, 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?w=400', '🦐', 499, 1),
('item-6', 'cat-2', 'ปลาหมึกกรอบ', 0.00, 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=400', '🦑', 299, 1),
('item-7', 'cat-3', 'ชุดผักรวมมิตร', 0.00, 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=400', '🥬', 299, 1),
('item-8', 'cat-3', 'เห็ดเข็มทอง wrap', 0.00, 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400', '🍄', 299, 1),
('item-9', 'cat-4', 'เต้าหู้ชีสทะลัก', 0.00, 'https://images.unsplash.com/photo-1541529086526-db283c563270?w=400', '🧀', 399, 1),
('item-10', 'cat-5', 'ซุปหมาล่าเข้มข้น', 0.00, '', '🌶️', 299, 1);

INSERT INTO `TABLES` (`id`, `table_number`, `status`) VALUES
('tb-1', '1', 'available'),
('tb-2', '2', 'available'),
('tb-3', '3', 'available'),
('tb-4', '4', 'available');
