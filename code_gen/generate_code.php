<?php
include 'database.php';

function generateSerialNumber($prefix) {
    // Generate a unique 4-digit serial number
    $stmt = $pdo->prepare("SELECT MAX(serial_number) as max_serial FROM items WHERE code_prefix = ?");
    $stmt->execute([$prefix]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $nextSerial = ($result['max_serial'] ? $result['max_serial'] + 1 : 1);
    return str_pad($nextSerial, 4, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $manualSerialNumber = $_POST['serial_number'];

    // Fetch category and subcategory codes
    $stmt = $pdo->prepare("SELECT code FROM categories WHERE id = ?");
    $stmt->execute([$category]);
    $categoryCode = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT code FROM subcategories WHERE id = ?");
    $stmt->execute([$subcategory]);
    $subcategoryCode = $stmt->fetchColumn();

    // Lester Code (random 2 characters)
    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lesterCode = $letters[rand(0, 25)] . $letters[rand(0, 25)];

    // Serial Number Logic
    $serialNumber = $manualSerialNumber ?? generateSerialNumber($categoryCode . $subcategoryCode);

    // Combine all parts
    $itemCode = $categoryCode . $subcategoryCode . $lesterCode . $serialNumber;

    // Store in database
    $stmt = $pdo->prepare("INSERT INTO items (code, category_id, subcategory_id, serial_number) VALUES (?, ?, ?, ?)");
    $stmt->execute([$itemCode, $category, $subcategory, $serialNumber]);

    echo json_encode(['item_code' => $itemCode]);
}
?>