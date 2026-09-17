<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = trim($data['id'] ?? '');

if ($id === '') {
    echo json_encode(["status" => "error", "message" => "ต้องระบุ id"]);
    exit();
}

try {
    $check = $pdo->prepare("SELECT id FROM MENU_ITEMS WHERE id = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        echo json_encode(["status" => "error", "message" => "ไม่พบวัตถุดิบนี้"]);
        exit();
    }

    // ORDER_ITEMS อ้างอิงแบบ CASCADE ลบตรงนี้จะพาประวัติออเดอร์เก่าหายไปด้วย
    $orderCheck = $pdo->prepare("SELECT COUNT(*) AS cnt FROM ORDER_ITEMS WHERE menu_item_id = ?");
    $orderCheck->execute([$id]);
    if ((int)$orderCheck->fetch()['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "ไม่สามารถลบได้ เนื่องจากมีประวัติออเดอร์ของวัตถุดิบนี้อยู่แล้ว กรุณาปรับสถานะเป็น \"หมด\" แทนการลบ"]);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM MENU_ITEMS WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["status" => "success", "message" => "ลบวัตถุดิบเรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
