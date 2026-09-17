<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);
$menu_item_id = $data['menu_item_id'] ?? '';

if (!$menu_item_id) {
    echo json_encode(["status" => "error", "message" => "ต้องระบุ menu_item_id"]);
    exit();
}

try {
    $pdo->beginTransaction();

    // ปิดสถานะสต็อกเมนูนี้ กันลูกค้าคนอื่นสั่งซ้ำ
    $stmtMenu = $pdo->prepare("UPDATE MENU_ITEMS SET is_available = 0 WHERE id = ?");
    $stmtMenu->execute([$menu_item_id]);

    // แจ้งเตือนทุกออเดอร์ที่ยังไม่เสิร์ฟและมีเมนูนี้ค้างอยู่ ไม่ว่าจะโต๊ะไหน
    $stmtItems = $pdo->prepare(
        "UPDATE ORDER_ITEMS oi
         JOIN ORDERS o ON o.id = oi.order_id
         SET oi.item_status = 'unavailable'
         WHERE oi.menu_item_id = ? AND o.status != 'served'"
    );
    $stmtItems->execute([$menu_item_id]);
    $affected = $stmtItems->rowCount();

    $pdo->commit();
    echo json_encode(["status" => "success", "message" => "แจ้งของหมดเรียบร้อยแล้ว", "affected_items" => $affected]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
