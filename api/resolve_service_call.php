<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['id'])) {
    echo json_encode(["status" => "error", "message" => "ต้องระบุ id"]);
    exit();
}

$id = $data['id'];

try {
    $check = $pdo->prepare("SELECT id FROM SERVICE_CALLS WHERE id = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        echo json_encode(["status" => "error", "message" => "ไม่พบรายการนี้"]);
        exit();
    }

    $pdo->prepare("UPDATE SERVICE_CALLS SET status = 'done' WHERE id = ?")->execute([$id]);

    echo json_encode(["status" => "success", "message" => "รับทราบแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
