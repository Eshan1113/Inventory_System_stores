<?php
// export_excel.php

// 1) Include config for DB connection
include 'config.php';

// 2) Capture GET parameters
$search            = isset($_GET['search'])            ? trim($_GET['search']) : '';
$transaction_type  = isset($_GET['transaction_type'])  ? trim($_GET['transaction_type']) : '';
$start_date        = isset($_GET['start_date'])        ? trim($_GET['start_date']) : '';
$end_date          = isset($_GET['end_date'])          ? trim($_GET['end_date']) : '';

// 3) Build WHERE conditions (same logic as in issued_fetch.php)
$conditions = [];
$params = [];

if (!empty($transaction_type)) {
    $conditions[] = "t.transaction_type = :transaction_type";
    $params[':transaction_type'] = $transaction_type;
}

if (!empty($search)) {
    // Match item_name, full_name, group_name, or remarks
    $conditions[] = "(i.item_name LIKE :search 
                    OR e.full_name LIKE :search 
                    OR g.group_name LIKE :search 
                    OR t.remarks LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($start_date)) {
    $conditions[] = "t.transaction_date >= :start_date";
    $params[':start_date'] = $start_date;
}

if (!empty($end_date)) {
    $conditions[] = "t.transaction_date <= :end_date";
    $params[':end_date'] = $end_date;
}

// Join all conditions
$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

// 4) Build the final SQL query
$sql = "
    SELECT 
        t.transaction_id,
        i.item_name,  
        e.full_name AS employee_name,
        g.group_name,
        t.transaction_date,
        t.transaction_type,
        t.quantity,
        t.remarks,
        t.created_by
    FROM item_transactions t
    JOIN items ini ON t.item_id = ini.item_id
    JOIN item_name_list i ON ini.item_name = i.id
    JOIN employees e ON t.employee_id = e.employee_id
    JOIN employee_groups g ON e.group_id = g.group_id
    $whereClause
    ORDER BY t.transaction_date DESC
";

// 5) Prepare & bind
$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();

// 6) Fetch all matching rows
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 7) Set headers to force the browser to download as .xls
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=filtered_transactions.xls");
header("Pragma: no-cache");
header("Expires: 0");

// 8) Output an HTML table with the data
echo "<table border='1'>";
echo "<tr>
        <th>#</th>
        <th>Item Name</th>
        <th>Employee</th>
        <th>Group</th>
        <th>Transaction Date</th>
        <th>Type</th>
        <th>Quantity</th>
        <th>Remarks</th>
      </tr>";

$i = 1;
if (!empty($transactions)) {
    foreach ($transactions as $row) {
        echo "<tr>";
        echo "<td>" . $i++ . "</td>";
        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['employee_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['group_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['transaction_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['transaction_type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "<td>" . htmlspecialchars($row['remarks'] ?? '-') . "</td>";
        echo "</tr>";
    }
} else {
    // If no rows found, add a single row
    echo "<tr><td colspan='8' style='text-align:center;'>No transactions found.</td></tr>";
}

echo "</table>";
?>
