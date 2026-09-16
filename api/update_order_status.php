<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);
$allowedStatuses = ['pending', 'cooking', 'served'];

if (!$data || empty($data['order_id']) || empty($data['status']) || !in_array($data['status'], $allowedStatuses, true)) {
    echo json_encode(["status" => "error", "message" => "ข้อมูลไม่ถูกต้อง"]);
    exit();
}

$order_id = $data['order_id'];
$newStatus = $data['status'];

try {
    $check = $pdo->prepare("SELECT id FROM ORDERS WHERE id = ?");
    $check->execute([$order_id]);
    if (!$check->fetch()) {
        echo json_encode(["status" => "error", "message" => "ไม่พบออเดอร์นี้"]);
        exit();
    }

    $pdo->prepare("UPDATE ORDERS SET status = ? WHERE id = ?")->execute([$newStatus, $order_id]);
    $pdo->prepare("UPDATE ORDER_ITEMS SET item_status = ? WHERE order_id = ?")->execute([$newStatus, $order_id]);

    echo json_encode(["status" => "success", "message" => "อัปเดตสถานะเรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
