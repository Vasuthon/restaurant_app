<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['table_id'])) {
    echo json_encode(["status" => "error", "message" => "ต้องระบุ table_id"]);
    exit();
}

// รายการแพ็กเกจที่อนุญาต กำหนดราคา/ชื่อฝั่งเซิร์ฟเวอร์เท่านั้น ห้ามใช้ค่าที่ client ส่งมาโดยตรง
$packages = [
    '299' => ['name' => 'Standard Mala', 'price' => 299],
    '399' => ['name' => 'Premium Pork & Beef', 'price' => 399],
    '499' => ['name' => 'Seafood & Wagyu Supreme', 'price' => 499],
];

$table_id = $data['table_id'];
$package_id = (string)($data['package_id'] ?? '399');

if (!isset($packages[$package_id])) {
    echo json_encode(["status" => "error", "message" => "แพ็กเกจไม่ถูกต้อง"]);
    exit();
}

$package = $packages[$package_id];
$adults = max(1, (int)($data['adults'] ?? 1));
$children = max(0, (int)($data['children'] ?? 0));

try {
    $pdo->beginTransaction();

    // ล็อกแถวโต๊ะไว้กันกรณีพนักงาน 2 คนกดเปิดโต๊ะเดียวกันพร้อมกัน
    $stmtCheck = $pdo->prepare("SELECT status FROM TABLES WHERE id = ? FOR UPDATE");
    $stmtCheck->execute([$table_id]);
    $table = $stmtCheck->fetch();

    if (!$table) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "ไม่พบโต๊ะนี้"]);
        exit();
    }

    if ($table['status'] === 'occupied') {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "โต๊ะนี้ถูกเปิดใช้งานอยู่แล้ว"]);
        exit();
    }

    if ($table['status'] === 'disabled') {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "โต๊ะนี้ปิดใช้งานชั่วคราวอยู่"]);
        exit();
    }

    $session_id = generateUUID();
    $qr_token = bin2hex(random_bytes(16)); // token สุ่มแยกจาก id กันคนเดา URL

    $stmtSession = $pdo->prepare(
        "INSERT INTO TABLE_SESSIONS (id, table_id, qr_code_token, status, package_name, package_price, adults, children, duration_minutes)
         VALUES (?, ?, ?, 'open', ?, ?, ?, ?, 120)"
    );
    $stmtSession->execute([$session_id, $table_id, $qr_token, $package['name'], $package['price'], $adults, $children]);

    $stmtTable = $pdo->prepare("UPDATE TABLES SET status = 'occupied' WHERE id = ?");
    $stmtTable->execute([$table_id]);

    $pdo->commit();

    echo json_encode([
        "status"        => "success",
        "message"       => "เปิดโต๊ะเรียบร้อยแล้ว",
        "session_id"    => $session_id,
        "qr_code_token" => $qr_token
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>