<?php
include 'database.php';

$categoryId = $_GET['category_id'];

if ($categoryId) {
    $stmt = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = ?");
    $stmt->execute([$categoryId]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($subcategories);
}
?>
