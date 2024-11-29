<?php
// Include database connection
include 'config.php';

// Get the filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build the SQL query
$sql = "SELECT i.*, c.category_name, l.location_name, inl.item_name 
        FROM items i
        LEFT JOIN item_categories c ON i.category_id = c.category_id
        LEFT JOIN locations l ON i.location_id = l.location_id
        LEFT JOIN item_name_list inl ON i.item_name = inl.id  -- Corrected the column name
        WHERE 1";

if ($search) {
    $sql .= " AND (inl.item_name LIKE :search)";
}
if ($startDate && $endDate) {
    $sql .= " AND i.purchase_date BETWEEN :start_date AND :end_date";
} elseif ($startDate) {
    $sql .= " AND i.purchase_date >= :start_date";
} elseif ($endDate) {
    $sql .= " AND i.purchase_date <= :end_date";
}

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind parameters
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
if ($startDate) {
    $stmt->bindValue(':start_date', $startDate);
}
if ($endDate) {
    $stmt->bindValue(':end_date', $endDate);
}

// Execute the statement
$stmt->execute();

// Fetch the results
$items = $stmt->fetchAll();

foreach ($items as $item) {
    echo "<tr>
            <td class='border px-4 py-2'>{$item['item_code']}</td>
            <td class='border px-4 py-2'>{$item['local_item_code']}</td>
            <td class='border px-4 py-2'>{$item['item_name']}</td>
            <td class='border px-4 py-2'>{$item['specifications']}</td>
            <td class='border px-4 py-2'>
                <img src='{$item['image_url']}' 
                     data-src='{$item['image_url']}' 
                     alt='Image' 
                     class='image-preview cursor-pointer' width='50'>
            </td>
            <td class='border px-4 py-2'>{$item['location_name']}</td>
            <td class='border px-4 py-2'>{$item['category_name']}</td>
            <td class='border px-4 py-2'>{$item['quantity']}</td>
            <td class='border px-4 py-2'>{$item['purchase_date']}</td>
            <td class='border px-4 py-2'>{$item['purchase_price']}</td>
            <td class='border px-4 py-2'>{$item['status']}</td>
          </tr>";
}
?>
