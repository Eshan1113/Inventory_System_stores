<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subcategoryName = $_POST['subcategory_name'];
    $subcategoryCode = $_POST['subcategory_code'];
    $categoryId = $_POST['category_id'];

    // Validate inputs
    if (strlen($subcategoryCode) === 2 && !empty($subcategoryName) && !empty($categoryId)) {
        $stmt = $pdo->prepare("INSERT INTO subcategories (category_id, name, code) VALUES (?, ?, ?)");

        try {
            $stmt->execute([$categoryId, $subcategoryName, $subcategoryCode]);
            echo json_encode(['status' => 'success', 'message' => 'Subcategory added successfully']);
        } catch(PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Subcategory could not be added']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
}
?>
