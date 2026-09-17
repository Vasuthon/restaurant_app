<?php
require_once "../config/db.php";

try {
    // เวลาคงเหลือคำนวณฝั่ง DB กันแต่ละอุปกรณ์เห็นค่าไม่ตรงกัน
    $sql = "SELECT t.id, t.table_number, t.status,
                   s.id AS session_id, s.qr_code_token, s.opened_at,
                   s.package_name, s.package_price, s.adults, s.children, s.duration_minutes,
                   GREATEST(0, (s.duration_minutes * 60) - TIMESTAMPDIFF(SECOND, s.opened_at, NOW())) AS time_left_seconds
            FROM TABLES t
            LEFT JOIN TABLE_SESSIONS s
                   ON s.table_id = t.id AND s.status = 'open'
            ORDER BY t.table_number";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tables = $stmt->fetchAll();

    echo json_encode(["status" => "success", "data" => $tables]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>