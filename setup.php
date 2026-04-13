<?php
// setup.php — One-time database initialiser
// Visit: http://localhost/SPORTIFY/setup.php
// DELETE this file after first run!

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host = " . DB_HOST, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE =>  PDO::ERRMODE_EXCEPTION,
    ]);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `sportify` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `sportify`");

    // Drop and recreate tables
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    foreach (['revenue_log','reviews','bookings','tournament_photos','tournaments','venue_photos','venues','users'] as $tbl) {
        $pdo->exec("DROP TABLE IF EXISTS `$tbl`");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Users
    $pdo->exec("CREATE TABLE `users` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(150) NOT NULL,
        `email` VARCHAR(200) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `role` ENUM('customer','seller','admin') NOT NULL DEFAULT 'customer',
        `status` ENUM('active','suspended') NOT NULL DEFAULT 'active',
        `phone` VARCHAR(20) DEFAULT NULL,
        `city` VARCHAR(100) DEFAULT NULL,
        `profile_pic` VARCHAR(300) DEFAULT NULL,
        `business_name` VARCHAR(200) DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_email` (`email`),
        KEY `idx_role` (`role`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

    // Venues
    $pdo->exec("CREATE TABLE `venues` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `seller_id` INT UNSIGNED NOT NULL,
        `name` VARCHAR(200) NOT NULL,
        `sport_type` ENUM('Cricket','Football','Badminton','Basketball','Tennis','Swimming','Others') NOT NULL,
        `location` VARCHAR(300) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `price_per_slot` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `slot_duration` ENUM('30','60','120') NOT NULL DEFAULT '60',
        `operating_hours_start` TIME NOT NULL DEFAULT '06:00:00',
        `operating_hours_end` TIME NOT NULL DEFAULT '22:00:00',
        `rating_avg` DECIMAL(3,2) DEFAULT 0.00,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_seller` (`seller_id`),
        KEY `idx_rating` (`rating_avg`),
        KEY `idx_sport` (`sport_type`),
        KEY `idx_active` (`is_active`,`is_deleted`),
        CONSTRAINT `fk_venue_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

    // Venue Photos
    $pdo->exec("CREATE TABLE `venue_photos` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `venue_id` INT UNSIGNED NOT NULL,
        `photo_url` VARCHAR(500) NOT NULL,
        `sort_order` INT NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `idx_venue_photo` (`venue_id`),
        CONSTRAINT `fk_photo_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

    // Tournaments
    $pdo->exec("CREATE TABLE `tournaments` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `seller_id` INT UNSIGNED NOT NULL,
        `name` VARCHAR(200) NOT NULL,
        `sport_type` ENUM('Cricket','Football','Badminton','Basketball','Tennis','Swimming','Others') NOT NULL,
        `location` VARCHAR(300) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `start_date` DATE NOT NULL,
        `end_date` DATE NOT NULL,
        `registration_deadline` DATE DEFAULT NULL,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_t_seller` (`seller_id`),
        KEY `idx_t_start` (`start_date`),
        CONSTRAINT `fk_tournament_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

    // Tournament Photos
    $pdo->exec("CREATE TABLE `tournament_photos` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `tournament_id` INT UNSIGNED NOT NULL,
        `photo_url` VARCHAR(500) NOT NULL,
        `sort_order` INT NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `idx_t_photo` (`tournament_id`),
        CONSTRAINT `fk_tphoto_tournament` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

    // Bookings
    $pdo->exec("CREATE TABLE `bookings` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `reference` VARCHAR(20) NOT NULL,
        `customer_id` INT UNSIGNED NOT NULL,
        `venue_id` INT UNSIGNED NOT NULL,
        `slot_date` DATE NOT NULL,
        `slot_start` TIME NOT NULL,
        `slot_end` TIME NOT NULL,
        `status` ENUM('confirmed','dismissed') NOT NULL DEFAULT 'confirmed',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_reference` (`reference`),
        UNIQUE KEY `uq_slot` (`venue_id`,`slot_date`,`slot_start`),
        KEY `idx_customer` (`customer_id`),
        KEY `idx_slot_date` (`slot_date`),
        CONSTRAINT `fk_booking_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_booking_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

    // Reviews
    $pdo->exec("CREATE TABLE `reviews` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `customer_id` INT UNSIGNED NOT NULL,
        `venue_id` INT UNSIGNED NOT NULL,
        `booking_id` INT UNSIGNED NOT NULL,
        `rating` TINYINT NOT NULL,
        `comment` VARCHAR(500) DEFAULT NULL,
        `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_review_booking` (`booking_id`),
        KEY `idx_venue_review` (`venue_id`),
        CONSTRAINT `fk_review_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_review_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_review_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

    // Revenue Log
    $pdo->exec("CREATE TABLE `revenue_log` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `seller_id` INT UNSIGNED NOT NULL,
        `booking_id` INT UNSIGNED NOT NULL,
        `amount` DECIMAL(10,2) NOT NULL,
        `recorded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_rev_seller` (`seller_id`),
        CONSTRAINT `fk_rev_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_rev_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

    // Seed admin
    $adminHash = password_hash('admin@123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO `users` (name, email, password, role, status) VALUES (?,?,?,?,?)");
    $stmt->execute(['Admin', 'admin@gmail.com', $adminHash, 'admin', 'active']);

    // Seed sample seller
    $sellerHash = password_hash('seller123', PASSWORD_BCRYPT);
    $stmt->execute(['Priya Sports Complex', 'seller@demo.com', $sellerHash, 'seller', 'active']);
    $sellerId = $pdo->lastInsertId();
    $pdo->prepare("UPDATE users SET business_name = ?, phone = ?, city = ? WHERE id = ?")
        ->execute(['Priya Multi-Sport Complex', '9876543210', 'Mumbai', $sellerId]);

    // Seed sample customer
    $custHash = password_hash('customer123', PASSWORD_BCRYPT);
    $stmt->execute(['Arjun Sharma', 'customer@demo.com', $custHash, 'customer', 'active']);
    $customerId = $pdo->lastInsertId();
    $pdo->prepare("UPDATE users SET phone = ?, city = ? WHERE id = ?")
        ->execute(['9123456789', 'Mumbai', $customerId]);

    // Seed sample venues
    $venues = [
        [$sellerId, 'Green Turf Cricket Ground', 'Cricket', 'Andheri West, Mumbai', 'Premium synthetic turf with floodlights. Ideal for tape-ball and leather cricket.', 800, '60', '06:00:00', '22:00:00', 4.50],
        [$sellerId, 'Priya Football Arena', 'Football', 'Bandra East, Mumbai', 'Full-size football ground with artificial grass. Changing rooms available.', 1200, '60', '07:00:00', '21:00:00', 4.20],
        [$sellerId, 'Ace Badminton Court', 'Badminton', 'Santacruz, Mumbai', '3 professional badminton courts with wooden flooring and proper lighting.', 300, '60', '05:30:00', '23:00:00', 3.80],
        [$sellerId, 'Slam Dunk Basketball Court', 'Basketball', 'Goregaon West, Mumbai', 'Full-size NBA standard court. Ideal for 5v5 matches and practice.', 600, '60', '06:00:00', '22:00:00', 4.70],
        [$sellerId, 'Tennis Hub Worli', 'Tennis', 'Worli, Mumbai', 'Synthetic clay court. Perfect for singles and doubles. Coaching available.', 500, '60', '06:00:00', '20:00:00', 4.00],
        [$sellerId, 'AquaLife Swimming Pool', 'Swimming', 'Powai, Mumbai', 'Olympic-size pool with 8 lanes. Timing systems and lifeguards available.', 400, '60', '05:00:00', '22:00:00', 4.60],
    ];
    foreach ($venues as $i =>  $v) {
        $pdo->prepare("INSERT INTO venues (seller_id,name,sport_type,location,description,price_per_slot,slot_duration,operating_hours_start,operating_hours_end,rating_avg,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,1)")
            ->execute($v);
    }

    // Seed upload directories
    $dirs = [
        __DIR__ . '/assets/uploads/venues',
        __DIR__ . '/assets/uploads/profiles',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        file_put_contents($dir . '/.gitkeep', '');
    }

    echo '<!DOCTYPE html><html><head><title>Sportify Setup</title>
    <style>body{font-family:sans-serif;max-width:600px;margin:4rem auto;padding:2rem;background:#f8f9fa}
    .success{background:#d1fae5;border:2px solid #10b981;border-radius:8px;padding:1.5rem;margin-bottom:1rem}
    .cred{background:#fff;border:1px solid #dee2e6;border-radius:6px;padding:.8rem 1rem;font-family:monospace;margin:.4rem 0}
    h2{color:#1A6B3C}a{background:#1A6B3C;color:white;padding:.6rem 1.4rem;border-radius:6px;text-decoration:none;display:inline-block;margin-top:1rem}</style></head><body>
    <h2>✅ Sportify Setup Complete!</h2>
    <div class = "success">
      <strong>Database created and seeded successfully.</strong><br><br>
      <b>Admin Login:</b><div class = "cred">Email: admin@gmail.com &nbsp;|&nbsp; Password: admin@123</div>
      <b>Seller Demo:</b><div class = "cred">Email: seller@demo.com &nbsp;|&nbsp; Password: seller123</div>
      <b>Customer Demo:</b><div class = "cred">Email: customer@demo.com &nbsp;|&nbsp; Password: customer123</div>
    </div>
    <p style = "color:#dc3545"><strong>⚠️ Delete this file (setup.php) after use for security!</strong></p>
    <a href = "<? = BASE_URL ?>/login.php">→ Go to Login</a>
    </body></html>';

} catch (PDOException $e) {
    echo '<h2 style = "color:red">Setup Failed</h2><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}







