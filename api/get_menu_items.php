<?php
require_once "../config/db.php";

try {
    $stmt = $pdo->prepare(
        "SELECT m.id, m.category_id, c.name AS category_name, m.name, m.description,
                m.price, m.image_url, m.emoji, m.min_package, m.is_available
         FROM MENU_ITEMS m
         JOIN CATEGORIES c ON c.id = m.category_id
         ORDER BY c.name, m.name"
    );
    $stmt->execute();
    $items = $stmt->fetchAll();

    echo json_encode(["status" => "success", "data" => $items]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
