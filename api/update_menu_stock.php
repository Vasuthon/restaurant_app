<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['menu_item_id']) || !array_key_exists('is_available', $data)) {
    echo json_encode(["status" => "error", "message" => "ข้อมูลไม่ถูกต้อง"]);
    exit();
}

$menu_item_id = $data['menu_item_id'];
$is_available = $data['is_available'] ? 1 : 0;

try {
    $check = $pdo->prepare("SELECT id FROM MENU_ITEMS WHERE id = ?");
    $check->execute([$menu_item_id]);
    if (!$check->fetch()) {
        echo json_encode(["status" => "error", "message" => "ไม่พบเมนูนี้"]);
        exit();
    }

    $stmt = $pdo->prepare("UPDATE MENU_ITEMS SET is_available = ? WHERE id = ?");
    $stmt->execute([$is_available, $menu_item_id]);

    echo json_encode(["status" => "success", "message" => "อัปเดตสถานะสต็อกแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
