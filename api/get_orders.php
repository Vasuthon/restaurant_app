<?php
require_once "../config/auth.php";
require_once "../config/db.php";

// เปิดให้ลูกค้าดูออเดอร์ของโต๊ะตัวเองได้โดยไม่ต้องล็อกอิน (ต้องระบุ session_id/table_id ของตัวเอง)
// แต่ถ้าไม่กรองอะไรเลย (มุมมองครัว/พนักงานเห็นทุกโต๊ะ) ต้องล็อกอินก่อน
if (empty($_GET['session_id']) && empty($_GET['table_id'])) {
    requireStaffAuth();
}

try {
    $where = [];
    $params = [];

    if (!empty($_GET['session_id'])) {
        $where[] = "os.id = ?";
        $params[] = $_GET['session_id'];
    }
    if (!empty($_GET['table_id'])) {
        $where[] = "os.table_id = ?";
        $params[] = $_GET['table_id'];
    }

    $whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";
    // ไม่กรองอะไรเลย (มุมมองครัว/พนักงาน) จำกัดจำนวนรายการล่าสุดกันข้อมูลโตไม่จำกัดเมื่อใช้งานจริงไปนานๆ
    $limitSql = $where ? "" : "LIMIT 100";

    $sql = "SELECT o.id, o.status, o.note, o.created_at, os.table_id, os.id AS session_id
            FROM ORDERS o
            JOIN TABLE_SESSIONS os ON os.id = o.session_id
            $whereSql
            ORDER BY o.created_at DESC
            $limitSql";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    if ($orders) {
        $orderIds = array_column($orders, 'id');
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

        $stmtItems = $pdo->prepare(
            "SELECT oi.id, oi.order_id, oi.menu_item_id, oi.quantity, oi.note, m.name
             FROM ORDER_ITEMS oi
             JOIN MENU_ITEMS m ON m.id = oi.menu_item_id
             WHERE oi.order_id IN ($placeholders)"
        );
        $stmtItems->execute($orderIds);

        $itemsByOrder = [];
        foreach ($stmtItems->fetchAll() as $item) {
            $itemsByOrder[$item['order_id']][] = $item;
        }

        foreach ($orders as &$order) {
            $order['items'] = $itemsByOrder[$order['id']] ?? [];
        }
        unset($order);
    }

    echo json_encode(["status" => "success", "data" => $orders]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
