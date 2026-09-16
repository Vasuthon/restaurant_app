<?php
require_once "../config/auth.php";
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);
$pin = (string)($data['pin'] ?? '');
$staffPin = getenv('STAFF_PIN') ?: '1234';

if ($pin === '' || !hash_equals($staffPin, $pin)) {
    echo json_encode(["status" => "error", "message" => "PIN ไม่ถูกต้อง"]);
    exit();
}

$_SESSION['staff_authenticated'] = true;
echo json_encode(["status" => "success", "message" => "เข้าสู่ระบบพนักงานเรียบร้อยแล้ว"]);
?>
