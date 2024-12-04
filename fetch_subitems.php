<?php
require_once 'config.php';

// Handle errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Get the search term and optional item ID from the AJAX request
    $searchTerm = isset($_GET['term']) ? $_GET['term'] : '';
    $itemId = isset($_GET['item_id']) ? $_GET['item_id'] : null;

    // Prepare SQL query with filtering by item_id and search term
    $query = "SELECT id, sub_item_name FROM sub_item_list WHERE sub_item_name LIKE ?";
    $params = ['%' . $searchTerm . '%'];

    if ($itemId) {
        $query .= " AND item_id = ?";
        $params[] = $itemId; // Bind item ID if provided
    }

    $query .= " ORDER BY sub_item_name ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    // Fetch results as an associative array
    $subItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($subItems);
} catch (Exception $e) {
    // Handle errors
    error_log("Error: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to fetch sub-items.']);
}
?>
