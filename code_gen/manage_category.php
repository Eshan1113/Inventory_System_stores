<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = $_POST['category_name'];
    $categoryCode = $_POST['category_code'];

    // Validate inputs
    if (strlen($categoryCode) === 2 && !empty($categoryName)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, code) VALUES (?, ?)");
        
        try {
            $stmt->execute([$categoryName, $categoryCode]);
            echo json_encode(['status' => 'success', 'message' => 'Category added successfully']);
        } catch(PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Category could not be added']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
}
?>