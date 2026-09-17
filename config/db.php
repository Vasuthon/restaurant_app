<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// ค่า default ชี้ไปที่ MySQL ของ Docker Compose (container restaurant_app_db, เปิดพอร์ต 3307 ให้เครื่อง host)
// เพราะข้อมูลทั้งหมดถูก migrate ไปรวมไว้ที่ Docker แล้ว ส่วน container web ของ Docker เอง
// จะมี environment variable (DB_HOST=db เป็นต้น) ตั้งไว้อยู่แล้วจาก docker-compose.yml จึงไม่ใช้ค่า default นี้
$host     = getenv('DB_HOST') ?: '127.0.0.1';
$port     = getenv('DB_PORT') ?: '3307';
$dbname   = getenv('DB_NAME') ?: 'restaurant_db';
$username = getenv('DB_USER') ?: 'restaurant_user';
$password = getenv('DB_PASSWORD') ?: 'restaurant_secret_change_me';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// ฟังก์ชันสำหรับสร้าง UUID v4 ใน PHP
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
?>