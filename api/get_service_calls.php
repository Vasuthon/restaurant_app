<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

try {
    $stmt = $pdo->prepare(
        "SELECT sc.id, sc.table_id, sc.reason, sc.status, sc.created_at, t.table_number
         FROM SERVICE_CALLS sc
         JOIN TABLES t ON t.id = sc.table_id
         WHERE sc.status = 'pending'
         ORDER BY sc.created_at ASC"
    );
    $stmt->execute();

    echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
