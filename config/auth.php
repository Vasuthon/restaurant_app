<?php
session_start();

function requireStaffAuth() {
    if (empty($_SESSION['staff_authenticated'])) {
        http_response_code(401);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(["status" => "error", "message" => "กรุณาเข้าสู่ระบบพนักงานก่อน"]);
        exit();
    }
}
?>
