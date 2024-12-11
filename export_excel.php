<?php
// export_excel.php

// Include database connection
include 'config.php';

// Include PhpSpreadsheet
require 'vendor/autoload.php'; // Adjust the path if necessary

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('Your Company')
    ->setLastModifiedBy('Your Company')
    ->setTitle('Item List')
    ->setSubject('Item List')
    ->setDescription('Exported item list from the inventory system.');

// Add header row
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Items');

// Define header columns
$headers = [
    'A1' => 'Item Code',
    'B1' => 'Local Item Code',
    'C1' => 'Item Name',
    'D1' => 'Specifications',
    'E1' => 'Location',
    'F1' => 'Category',
    'G1' => 'Quantity',
    'H1' => 'Purchase Date',
    'I1' => 'Purchase Price'
];

// Apply headers
foreach ($headers as $cell => $header) {
    $sheet->setCellValue($cell, $header);
    // Optionally, style the header
    $sheet->getStyle($cell)->getFont()->setBold(true);
    $sheet->getColumnDimension(substr($cell, 0, 1))->setAutoSize(true);
}

// Populate data
$row = 2; // Starting from the second row
foreach ($items as $item) {
    $sheet->setCellValue('A' . $row, $item['item_code']);
    $sheet->setCellValue('B' . $row, $item['local_item_code']);
    $sheet->setCellValue('C' . $row, $item['item_name']);
    $sheet->setCellValue('D' . $row, $item['specifications']);
    $sheet->setCellValue('E' . $row, $item['location_name']);
    $sheet->setCellValue('F' . $row, $item['category_name']);
    $sheet->setCellValue('G' . $row, $item['quantity']);
    $sheet->setCellValue('H' . $row, $item['purchase_date']);
    $sheet->setCellValue('I' . $row, $item['purchase_price']);
    $row++;
}

// Create a writer and output the file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="item_list.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
