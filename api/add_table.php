<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);
$table_number = trim($data['table_number'] ?? '');

if ($table_number === '') {
    echo json_encode(["status" => "error", "message" => "กรุณากรอกชื่อ/เลขโต๊ะ"]);
    exit();
}

try {
    $check = $pdo->prepare("SELECT id FROM TABLES WHERE table_number = ?");
    $check->execute([$table_number]);
    if ($check->fetch()) {
        echo json_encode(["status" => "error", "message" => "มีโต๊ะชื่อนี้อยู่แล้ว"]);
        exit();
    }

    $id = generateUUID();
    $stmt = $pdo->prepare("INSERT INTO TABLES (id, table_number, status) VALUES (?, ?, 'available')");
    $stmt->execute([$id, $table_number]);

    echo json_encode(["status" => "success", "message" => "เพิ่มโต๊ะเรียบร้อยแล้ว", "id" => $id]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
