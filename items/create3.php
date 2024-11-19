<?php
// items/create.php
require_once '../config.php';

if (!isLoggedIn() || !hasPermission($conn, $_SESSION['user_id'], 'create_item')) {
    header("Location: items/login.php");
    exit();
}

// Fetch locations for dropdown
$locations = $conn->query("SELECT location_id, location_name FROM locations")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate and sanitize input
        $itemName = sanitizeInput($_POST['item_name']);
        $specifications = sanitizeInput($_POST['specifications']);
        $location = filter_var($_POST['location'], FILTER_VALIDATE_INT);
        $category = sanitizeInput($_POST['category']);
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
        $originCountry = sanitizeInput($_POST['origin_country']);
        $warrantyUntil = $_POST['warranty_until'];
        $purchaseDate = $_POST['purchase_date'];
        $purchasePrice = filter_var($_POST['purchase_price'], FILTER_VALIDATE_FLOAT);

        // Validate required fields
        if (empty($itemName) || !$quantity || $quantity < 0) {
            throw new Exception("Please fill all required fields with valid values.");
        }

        // Insert new item
        $stmt = $conn->prepare("
            INSERT INTO items (
                item_name, specifications, `Item Location`, `Item Category`,
                quantity, origin_country, warranty_until, purchase_date,
                purchase_price, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $itemName, $specifications, $location, $category,
            $quantity, $originCountry, $warrantyUntil, $purchaseDate,
            $purchasePrice, $_SESSION['user_id']
        ]);

        logActivity($conn, $_SESSION['user_id'], 'CREATE', "Created new item: $itemName");
        
        $_SESSION['success'] = "Item created successfully";
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Item - Store Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include '../includes/navigation.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Create New Item</h1>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-