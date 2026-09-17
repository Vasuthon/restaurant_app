<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id          = trim($data['id'] ?? '');
$name        = trim($data['name'] ?? '');
$description = trim($data['description'] ?? '');

if ($id === '' || $name === '') {
    echo json_encode(["status" => "error", "message" => "กรุณากรอกชื่อหมวดหมู่"]);
    exit();
}

try {
    $check = $pdo->prepare("SELECT id FROM CATEGORIES WHERE id = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        echo json_encode(["status" => "error", "message" => "ไม่พบหมวดหมู่นี้"]);
        exit();
    }

    $stmt = $pdo->prepare("UPDATE CATEGORIES SET name = ?, description = ? WHERE id = ?");
    $stmt->execute([$name, $description !== '' ? $description : null, $id]);

    echo json_encode(["status" => "success", "message" => "แก้ไขหมวดหมู่เรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
