<?php
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['session_id']) || empty($data['items'])) {
    echo json_encode(["status" => "error", "message" => "ข้อมูลไม่ครบถ้วน"]);
    exit();
}

$session_id = $data['session_id'];
$note = $data['note'] ?? '';

try {
    $pdo->beginTransaction();

    // 1. เช็คก่อนว่า session นี้เปิดรับออเดอร์อยู่จริง (กันสั่งเข้าโต๊ะที่ปิดไปแล้ว)
    $stmtSession = $pdo->prepare("SELECT status FROM TABLE_SESSIONS WHERE id = ?");
    $stmtSession->execute([$session_id]);
    $session = $stmtSession->fetch();

    if (!$session) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "ไม่พบ session นี้"]);
        exit();
    }

    if ($session['status'] !== 'open') {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "โต๊ะนี้ปิดรับออเดอร์แล้ว"]);
        exit();
    }

    $order_id = generateUUID();

    // 2. บันทึกลงตาราง ORDERS
    $stmtOrder = $pdo->prepare("INSERT INTO ORDERS (id, session_id, status, note) VALUES (?, ?, 'pending', ?)");
    $stmtOrder->execute([$order_id, $session_id, $note]);

    // 3. ดึงราคา+สถานะเมนูจาก DB เองทุกรายการ ห้ามใช้ราคาที่ client ส่งมา
    $stmtMenu = $pdo->prepare("SELECT price, is_available FROM MENU_ITEMS WHERE id = ?");
    $stmtItem = $pdo->prepare("INSERT INTO ORDER_ITEMS (id, order_id, menu_item_id, quantity, price_at_order, note, item_status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");

    foreach ($data['items'] as $item) {
        if (empty($item['menu_item_id']) || empty($item['quantity']) || $item['quantity'] <= 0) {
            throw new Exception("รายการอาหารไม่ถูกต้อง");
        }

        $stmtMenu->execute([$item['menu_item_id']]);
        $menuItem = $stmtMenu->fetch();

        if (!$menuItem) {
            throw new Exception("ไม่พบเมนู: " . $item['menu_item_id']);
        }

        if (!$menuItem['is_available']) {
            throw new Exception("เมนูนี้หมดชั่วคราว ไม่สามารถสั่งได้: " . $item['menu_item_id']);
        }

        $order_item_id = generateUUID();
        $stmtItem->execute([
            $order_item_id,
            $order_id,
            $item['menu_item_id'],
            $item['quantity'],
            $menuItem['price'], // ใช้ราคาจาก DB เท่านั้น ไม่ใช้ $item['price'] จาก client
            $item['note'] ?? ''
        ]);
    }

    $pdo->commit();
    echo json_encode(["status" => "success", "message" => "สั่งอาหารเรียบร้อยแล้ว", "order_id" => $order_id]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>