<?php
require_once "../config/db.php";

try {
    $stmt = $pdo->prepare("SELECT id, name, description FROM CATEGORIES ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll();

    echo json_encode(["status" => "success", "data" => $categories]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>