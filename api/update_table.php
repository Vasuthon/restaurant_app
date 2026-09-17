<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = trim($data['id'] ?? '');

if ($id === '') {
    echo json_encode(["status" => "error", "message" => "ต้องระบุ id"]);
    exit();
}

try {
    $check = $pdo->prepare("SELECT status FROM TABLES WHERE id = ?");
    $check->execute([$id]);
    $table = $check->fetch();
    if (!$table) {
        echo json_encode(["status" => "error", "message" => "ไม่พบโต๊ะนี้"]);
        exit();
    }

    $sets = [];
    $params = [];

    if (array_key_exists('table_number', $data)) {
        $table_number = trim($data['table_number']);
        if ($table_number === '') {
            echo json_encode(["status" => "error", "message" => "กรุณากรอกชื่อ/เลขโต๊ะ"]);
            exit();
        }
        $dup = $pdo->prepare("SELECT id FROM TABLES WHERE table_number = ? AND id != ?");
        $dup->execute([$table_number, $id]);
        if ($dup->fetch()) {
            echo json_encode(["status" => "error", "message" => "มีโต๊ะชื่อนี้อยู่แล้ว"]);
            exit();
        }
        $sets[] = "table_number = ?";
        $params[] = $table_number;
    }

    if (array_key_exists('status', $data)) {
        $status = $data['status'];
        if (!in_array($status, ['available', 'disabled'], true)) {
            echo json_encode(["status" => "error", "message" => "สถานะไม่ถูกต้อง"]);
            exit();
        }
        if ($table['status'] === 'occupied') {
            echo json_encode(["status" => "error", "message" => "โต๊ะนี้มีลูกค้าอยู่ ปิด/เปิดใช้งานไม่ได้ในขณะนี้"]);
            exit();
        }
        $sets[] = "status = ?";
        $params[] = $status;
    }

    if (!$sets) {
        echo json_encode(["status" => "error", "message" => "ไม่มีข้อมูลที่จะแก้ไข"]);
        exit();
    }

    $params[] = $id;
    $stmt = $pdo->prepare("UPDATE TABLES SET " . implode(', ', $sets) . " WHERE id = ?");
    $stmt->execute($params);

    echo json_encode(["status" => "success", "message" => "แก้ไขโต๊ะเรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
