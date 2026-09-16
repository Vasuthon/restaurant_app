<?php
require_once "../config/auth.php";
header("Content-Type: application/json; charset=UTF-8");

$_SESSION = [];
session_destroy();

echo json_encode(["status" => "success", "message" => "ออกจากระบบพนักงานแล้ว"]);
?>
