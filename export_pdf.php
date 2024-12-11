<?php
// export_pdf.php

// Disable error reporting in production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Include database connection
include 'config.php';

// Include DOMPDF
require 'vendor/autoload.php'; // Ensure the path is correct

use Dompdf\Dompdf;

// Get the filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build the SQL query
$sql = "SELECT i.*, c.category_name, l.location_name, inl.item_name 
        FROM items i
        LEFT JOIN item_categories c ON i.category_id = c.category_id
        LEFT JOIN locations l ON i.location_id = l.location_id
        LEFT JOIN item_name_list inl ON i.item_name = inl.id
        WHERE 1";

$params = [];

if ($search) {
    $sql .= " AND (inl.item_name LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
if ($location) {
    $sql .= " AND (l.location_name LIKE :location)";
    $params[':location'] = '%' . $location . '%';
}
if ($category) {
    $sql .= " AND (c.category_name LIKE :category)";
    $params[':category'] = '%' . $category . '%';
}
if ($startDate && $endDate) {
    $sql .= " AND i.purchase_date BETWEEN :start_date AND :end_date";
    $params[':start_date'] = $startDate;
    $params[':end_date'] = $endDate;
} elseif ($startDate) {
    $sql .= " AND i.purchase_date >= :start_date";
    $params[':start_date'] = $startDate;
} elseif ($endDate) {
    $sql .= " AND i.purchase_date <= :end_date";
    $params[':end_date'] = $endDate;
}

$stmt = $conn->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();

$items = $stmt->fetchAll();

// Encode the logo image to Base64
$logoPath = 'nw.png'; // Ensure this path is correct relative to export_pdf.php

if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoExtension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));

    // Validate image extension
    $validExtensions = ['png', 'jpg', 'jpeg', 'gif'];
    if (in_array($logoExtension, $validExtensions)) {
        $logoSrc = 'data:image/' . $logoExtension . ';base64,' . $logoData;
    } else {
        $logoSrc = ''; // Unsupported image type
    }
} else {
    $logoSrc = ''; // Image file not found
}

// Start building the HTML
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Item List</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            margin: 20px;
            font-size: 12px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .logo { 
            width: 200px; /* Small size for the logo */
            height: auto; /* Maintain aspect ratio */
            margin-bottom: 10px;
        }
        h1 {
            font-size: 18px;
            margin: 0;
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        ' . ($logoSrc ? '<img src="' . $logoSrc . '" class="logo" alt="Logo" />' : '') . '
        
    </div>
    <table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Local Item Code</th>
                <th>Item Name</th>
                <th>Specifications</th>
                <th>Location</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Purchase Date</th>
                <th>Purchase Price</th>
            </tr>
        </thead>
        <tbody>';

foreach ($items as $item) {
    $html .= '<tr>
        <td>' . htmlspecialchars($item['item_code']) . '</td>
        <td>' . htmlspecialchars($item['local_item_code']) . '</td>
        <td>' . htmlspecialchars($item['item_name']) . '</td>
        <td>' . htmlspecialchars($item['specifications']) . '</td>
        <td>' . htmlspecialchars($item['location_name']) . '</td>
        <td>' . htmlspecialchars($item['category_name']) . '</td>
        <td>' . htmlspecialchars($item['quantity']) . '</td>
        <td>' . htmlspecialchars($item['purchase_date']) . '</td>
        <td>' . htmlspecialchars($item['purchase_price']) . '</td>
    </tr>';
}

$html .= '</tbody>
    </table>
</body>
</html>';

// Initialize DOMPDF
$dompdf = new Dompdf();

// (Optional) Enable remote content if necessary
$dompdf->set_option('isRemoteEnabled', true);

// Load the HTML content
$dompdf->loadHtml($html);

// Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Clear any previous output to prevent corruption
if (ob_get_length()) {
    ob_end_clean();
}

// Output the generated PDF (force download)
$dompdf->stream("item_list.pdf", ["Attachment" => true]);
?>
