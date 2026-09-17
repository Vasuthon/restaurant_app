<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['session_id'])) {
    echo json_encode(["status" => "error", "message" => "ต้องระบุ session_id"]);
    exit();
}

$session_id = $data['session_id'];

try {
    $stmtCheck = $pdo->prepare("SELECT status FROM TABLE_SESSIONS WHERE id = ?");
    $stmtCheck->execute([$session_id]);
    $session = $stmtCheck->fetch();

    if (!$session || $session['status'] !== 'open') {
        echo json_encode(["status" => "error", "message" => "ไม่พบโต๊ะที่เปิดใช้งานอยู่"]);
        exit();
    }

    $sets = [];
    $params = [];

    if (array_key_exists('adults', $data)) {
        $sets[] = "adults = ?";
        $params[] = max(1, (int)$data['adults']);
    }
    if (array_key_exists('children', $data)) {
        $sets[] = "children = ?";
        $params[] = max(0, (int)$data['children']);
    }

    if (!$sets) {
        echo json_encode(["status" => "error", "message" => "ไม่มีข้อมูลที่จะแก้ไข"]);
        exit();
    }

    $params[] = $session_id;
    $stmt = $pdo->prepare("UPDATE TABLE_SESSIONS SET " . implode(', ', $sets) . " WHERE id = ?");
    $stmt->execute($params);

    echo json_encode(["status" => "success", "message" => "แก้ไขโต๊ะเรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
