<?php
require_once "../config/auth.php";
requireStaffAuth();
require_once "../config/db.php";

$session_id = $_GET['session_id'] ?? '';
if ($session_id === '') {
    echo json_encode(["status" => "error", "message" => "ต้องระบุ session_id"]);
    exit();
}

try {
    $stmtSession = $pdo->prepare("SELECT * FROM TABLE_SESSIONS WHERE id = ?");
    $stmtSession->execute([$session_id]);
    $session = $stmtSession->fetch();

    if (!$session) {
        echo json_encode(["status" => "error", "message" => "ไม่พบ session นี้"]);
        exit();
    }

    $adults = (int)$session['adults'];
    $children = (int)$session['children'];
    $packagePrice = (float)$session['package_price'];
    $buffetCharge = $adults * $packagePrice + $children * $packagePrice * 0.5;

    // รวมรายการที่สั่งทั้งหมดของโต๊ะนี้ (ทุกออเดอร์) กลุ่มตามเมนู
    $stmtItems = $pdo->prepare(
        "SELECT oi.menu_item_id, m.name, c.name AS category_name, oi.price_at_order, SUM(oi.quantity) AS qty
         FROM ORDER_ITEMS oi
         JOIN ORDERS o ON o.id = oi.order_id
         JOIN MENU_ITEMS m ON m.id = oi.menu_item_id
         JOIN CATEGORIES c ON c.id = m.category_id
         WHERE o.session_id = ?
         GROUP BY oi.menu_item_id, m.name, c.name, oi.price_at_order"
    );
    $stmtItems->execute([$session_id]);
    $allItems = $stmtItems->fetchAll();

    $extraItems = [];
    $extraTotal = 0.0;
    $hasFreeSoup = false;

    foreach ($allItems as $it) {
        $unitPrice = (float)$it['price_at_order'];
        $qty = (float)$it['qty'];
        if ($unitPrice > 0) {
            $lineTotal = $unitPrice * $qty;
            $extraItems[] = [
                "name" => $it['name'],
                "qty" => $qty,
                "unit_price" => $unitPrice,
                "line_total" => $lineTotal,
            ];
            $extraTotal += $lineTotal;
        } elseif ($it['category_name'] === 'น้ำซุป') {
            $hasFreeSoup = true;
        }
    }

    // กฎร้าน: ทานคนเดียว (รวมผู้ใหญ่+เด็ก = 1) แล้วสั่งน้ำซุปฟรี เหมาจ่ายขั้นต่ำ 99 บาทต่อโต๊ะ
    $soloSoupCharge = 0.0;
    if (($adults + $children) === 1 && $hasFreeSoup) {
        $soloSoupCharge = 99.0;
    }

    $subtotal = $buffetCharge + $extraTotal + $soloSoupCharge;
    $vat = round($subtotal * 0.07, 2);
    $grandTotal = $subtotal + $vat;

    echo json_encode([
        "status" => "success",
        "data" => [
            "package_name" => $session['package_name'],
            "package_price" => $packagePrice,
            "adults" => $adults,
            "children" => $children,
            "buffet_charge" => $buffetCharge,
            "extra_items" => $extraItems,
            "extra_total" => $extraTotal,
            "solo_soup_charge" => $soloSoupCharge,
            "subtotal" => $subtotal,
            "vat" => $vat,
            "grand_total" => $grandTotal,
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
