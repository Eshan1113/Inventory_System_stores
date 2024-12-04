<?php
require_once 'config.php';

// Handle errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Get the search term from the AJAX request
    $searchTerm = isset($_GET['term']) ? $_GET['term'] : '';

    // Prepare SQL query with search functionality
    $stmt = $conn->prepare("SELECT id, item_name FROM item_name_list WHERE item_name LIKE ? ORDER BY item_name ASC");
    $stmt->execute(['%' . $searchTerm . '%']); // Bind the search term with wildcard

    // Fetch results as an associative array
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($items);
} catch (Exception $e) {
    // Handle errors
    error_log("Error: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to fetch items.']);
}
?>
