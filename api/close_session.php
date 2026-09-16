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
    $pdo->beginTransaction();

    $stmtSession = $pdo->prepare("SELECT table_id, status FROM TABLE_SESSIONS WHERE id = ? FOR UPDATE");
    $stmtSession->execute([$session_id]);
    $session = $stmtSession->fetch();

    if (!$session) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "ไม่พบ session นี้"]);
        exit();
    }

    if ($session['status'] !== 'open') {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "session นี้ปิดไปแล้ว"]);
        exit();
    }

    $stmtCloseSession = $pdo->prepare(
        "UPDATE TABLE_SESSIONS SET status = 'closed', closed_at = NOW() WHERE id = ?"
    );
    $stmtCloseSession->execute([$session_id]);

    $stmtTable = $pdo->prepare("UPDATE TABLES SET status = 'available' WHERE id = ?");
    $stmtTable->execute([$session['table_id']]);

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "ปิดโต๊ะเรียบร้อยแล้ว"]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>