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
    $check = $pdo->prepare("SELECT id FROM CATEGORIES WHERE id = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        echo json_encode(["status" => "error", "message" => "ไม่พบหมวดหมู่นี้"]);
        exit();
    }

    // กันการลบหมวดหมู่ที่ยังมีวัตถุดิบอยู่ เพราะ MENU_ITEMS อ้างอิงแบบ ON DELETE CASCADE
    // ถ้าลบตรงนี้จะทำให้วัตถุดิบทั้งหมดในหมวดนี้ถูกลบตามไปด้วย
    $itemCheck = $pdo->prepare("SELECT COUNT(*) AS cnt FROM MENU_ITEMS WHERE category_id = ?");
    $itemCheck->execute([$id]);
    if ((int)$itemCheck->fetch()['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "ไม่สามารถลบได้ เนื่องจากยังมีวัตถุดิบอยู่ในหมวดหมู่นี้ กรุณาย้ายหรือลบวัตถุดิบออกก่อน"]);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM CATEGORIES WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["status" => "success", "message" => "ลบหมวดหมู่เรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
