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
    $check = $pdo->prepare("SELECT status FROM TABLES WHERE id = ?");
    $check->execute([$id]);
    $table = $check->fetch();
    if (!$table) {
        echo json_encode(["status" => "error", "message" => "ไม่พบโต๊ะนี้"]);
        exit();
    }

    if ($table['status'] === 'occupied') {
        echo json_encode(["status" => "error", "message" => "โต๊ะนี้มีลูกค้าอยู่ ลบไม่ได้"]);
        exit();
    }

    // กันการลบโต๊ะที่เคยมีประวัติการเปิดโต๊ะ/ออเดอร์ เพราะ TABLE_SESSIONS อ้างอิงแบบ ON DELETE CASCADE
    // ถ้าลบตรงนี้จะทำให้ประวัติออเดอร์เก่าของโต๊ะนี้ถูกลบตามไปด้วย
    $sessionCheck = $pdo->prepare("SELECT COUNT(*) AS cnt FROM TABLE_SESSIONS WHERE table_id = ?");
    $sessionCheck->execute([$id]);
    if ((int)$sessionCheck->fetch()['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "ไม่สามารถลบได้ เนื่องจากโต๊ะนี้มีประวัติการใช้งานอยู่แล้ว กรุณาใช้ \"ปิดใช้งานชั่วคราว\" แทนการลบ"]);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM TABLES WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["status" => "success", "message" => "ลบโต๊ะเรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
