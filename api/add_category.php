<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name        = trim($data['name'] ?? '');
$description = trim($data['description'] ?? '');

if ($name === '') {
    echo json_encode(["status" => "error", "message" => "กรุณากรอกชื่อหมวดหมู่"]);
    exit();
}

try {
    $id = generateUUID();
    $stmt = $pdo->prepare("INSERT INTO CATEGORIES (id, name, description) VALUES (?, ?, ?)");
    $stmt->execute([$id, $name, $description !== '' ? $description : null]);

    echo json_encode(["status" => "success", "message" => "เพิ่มหมวดหมู่เรียบร้อยแล้ว", "id" => $id]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
