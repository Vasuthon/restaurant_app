<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => "error", "message" => "กรุณาเลือกไฟล์รูปภาพ"]);
    exit();
}

$file = $_FILES['image'];

$maxBytes = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $maxBytes) {
    echo json_encode(["status" => "error", "message" => "ไฟล์ใหญ่เกินไป (สูงสุด 5MB)"]);
    exit();
}

// ตรวจสอบว่าเป็นไฟล์รูปภาพจริง ไม่ใช่แค่นามสกุลไฟล์ (กันการอัปโหลดไฟล์อันตรายปลอมเป็นรูป)
$imageInfo = @getimagesize($file['tmp_name']);
if ($imageInfo === false) {
    echo json_encode(["status" => "error", "message" => "ไฟล์นี้ไม่ใช่รูปภาพที่ถูกต้อง"]);
    exit();
}

$allowedTypes = [
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG  => 'png',
    IMAGETYPE_WEBP => 'webp',
    IMAGETYPE_GIF  => 'gif',
];
$type = $imageInfo[2];
if (!isset($allowedTypes[$type])) {
    echo json_encode(["status" => "error", "message" => "รองรับเฉพาะไฟล์ JPG, PNG, WEBP หรือ GIF"]);
    exit();
}

$ext = $allowedTypes[$type];
$filename = generateUUID() . '.' . $ext;
$uploadDir = __DIR__ . '/../uploads/menu/';
$destPath = $uploadDir . $filename;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(["status" => "error", "message" => "บันทึกไฟล์ไม่สำเร็จ"]);
    exit();
}

echo json_encode(["status" => "success", "message" => "อัปโหลดรูปสำเร็จ", "image_url" => "uploads/menu/" . $filename]);
?>
