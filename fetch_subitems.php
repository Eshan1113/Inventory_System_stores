<?php
require_once 'config.php';

// Fetch sub-items based on item_id and search query
if (isset($_GET['id']) && isset($_GET['q'])) {
    $item_id = $_GET['id'];
    $search = $_GET['q'];
    
    $stmt = $conn->prepare("SELECT id, sub_item_name FROM sub_item_list WHERE id = ? AND sub_item_name LIKE ? LIMIT 10");
    $stmt->execute([$item_id, "%$search%"]);
    $sub_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sub_items' => $sub_items]);
}