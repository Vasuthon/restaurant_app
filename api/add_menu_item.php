<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$category_id  = trim($data['category_id'] ?? '');
$name         = trim($data['name'] ?? '');
$description  = trim($data['description'] ?? '');
$price        = $data['price'] ?? 0;
$image_url    = trim($data['image_url'] ?? '');
$emoji        = trim($data['emoji'] ?? '');
$min_package  = (int)($data['min_package'] ?? 0);
$is_available = array_key_exists('is_available', $data) ? ($data['is_available'] ? 1 : 0) : 1;

if ($category_id === '' || $name === '' || !is_numeric($price) || $price < 0) {
    echo json_encode(["status" => "error", "message" => "กรุณากรอกชื่อ หมวดหมู่ และราคาให้ถูกต้อง"]);
    exit();
}

try {
    $checkCat = $pdo->prepare("SELECT id FROM CATEGORIES WHERE id = ?");
    $checkCat->execute([$category_id]);
    if (!$checkCat->fetch()) {
        echo json_encode(["status" => "error", "message" => "ไม่พบหมวดหมู่นี้"]);
        exit();
    }

    $id = generateUUID();
    $stmt = $pdo->prepare(
        "INSERT INTO MENU_ITEMS (id, category_id, name, description, price, image_url, emoji, min_package, is_available)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$id, $category_id, $name, $description !== '' ? $description : null, $price, $image_url !== '' ? $image_url : null, $emoji !== '' ? $emoji : null, $min_package, $is_available]);

    echo json_encode(["status" => "success", "message" => "เพิ่มวัตถุดิบเรียบร้อยแล้ว", "id" => $id]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
