<?php
// models/Revenue.php
require_once __DIR__ . '/../config/db.php';

class Revenue {
    private ?PDO $db;
    public function __construct() { $this->db = getPDO(); }

    public function getSummary(int $sellerId): array {
        $sql = "SELECT 
            SUM(CASE WHEN b.slot_date = CURDATE() THEN r.amount ELSE 0 END) as today,
            SUM(CASE WHEN YEARWEEK(b.slot_date, 1) = YEARWEEK(CURDATE(), 1) THEN r.amount ELSE 0 END) as week,
            SUM(CASE WHEN MONTH(b.slot_date) = MONTH(CURDATE()) AND YEAR(b.slot_date) = YEAR(CURDATE()) THEN r.amount ELSE 0 END) as month,
            SUM(r.amount) as alltime
            FROM revenue_log r
            JOIN bookings b ON b.id = r.booking_id
            WHERE r.seller_id = ? AND b.status = 'confirmed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sellerId]);
        return $stmt->fetch() ?: ['today'=>0, 'week'=>0, 'month'=>0, 'alltime'=>0];
    }

    public function getDailyLast30(int $sellerId): array {
        $sql = "SELECT b.slot_date as day, SUM(r.amount) as total 
                FROM revenue_log r
                JOIN bookings b ON b.id = r.booking_id
                WHERE r.seller_id = ? AND b.slot_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND b.status = 'confirmed'
                GROUP BY b.slot_date ORDER BY b.slot_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll();
    }

    public function getRecentTransactions(int $sellerId, int $limit = 10): array {
        $sql = "SELECT r.*, b.reference, v.name as venue_name, u.name as customer_name, b.slot_date
                FROM revenue_log r
                JOIN bookings b ON b.id = r.booking_id
                JOIN venues v ON v.id = b.venue_id
                JOIN users u ON u.id = b.customer_id
                WHERE r.seller_id = ?
                ORDER BY r.recorded_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sellerId, $limit]);
        return $stmt->fetchAll();
    }
}
