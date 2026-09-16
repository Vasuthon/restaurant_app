<?php
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['session_id']) || empty($data['reason'])) {
    echo json_encode(["status" => "error", "message" => "ข้อมูลไม่ครบถ้วน"]);
    exit();
}

$session_id = $data['session_id'];
$reason = $data['reason'];

try {
    $stmtSession = $pdo->prepare("SELECT table_id, status FROM TABLE_SESSIONS WHERE id = ?");
    $stmtSession->execute([$session_id]);
    $session = $stmtSession->fetch();

    if (!$session || $session['status'] !== 'open') {
        echo json_encode(["status" => "error", "message" => "ไม่พบโต๊ะที่เปิดใช้งานอยู่"]);
        exit();
    }

    $id = generateUUID();
    $stmt = $pdo->prepare(
        "INSERT INTO SERVICE_CALLS (id, table_id, session_id, reason, status) VALUES (?, ?, ?, ?, 'pending')"
    );
    $stmt->execute([$id, $session['table_id'], $session_id, $reason]);

    echo json_encode(["status" => "success", "message" => "แจ้งพนักงานเรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
