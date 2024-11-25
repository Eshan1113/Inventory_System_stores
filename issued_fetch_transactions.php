<?php
include 'config.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';
$transaction_type = isset($_POST['transaction_type']) ? $_POST['transaction_type'] : '';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

$conditions = [];
if ($transaction_type) {
    $conditions[] = "t.transaction_type = '$transaction_type'";
}
if ($search) {
    $conditions[] = "(i.item_name LIKE '%$search%' 
                      OR e.full_name LIKE '%$search%' 
                      OR g.group_name LIKE '%$search%' 
                      OR t.remarks LIKE '%$search%')";
}
if ($start_date) {
    $conditions[] = "t.transaction_date >= '$start_date'";
}
if ($end_date) {
    $conditions[] = "t.transaction_date <= '$end_date'";
}

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$sql = "SELECT 
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
        JOIN items i ON t.item_id = i.item_id
        JOIN employees e ON t.employee_id = e.employee_id
        JOIN employee_groups g ON e.group_id = g.group_id
        $whereClause
        ORDER BY t.transaction_date DESC";

$result = $conn->query($sql);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

if ($rows) {
    foreach ($rows as $index => $transaction) {
        echo '<tr>';
        echo '<td class="border border-gray-300 px-4 py-2">' . ($index + 1) . '</td>';
        echo '<td class="border border-gray-300 px-4 py-2">' . htmlspecialchars($transaction['item_name']) . '</td>';
        echo '<td class="border border-gray-300 px-4 py-2">' . htmlspecialchars($transaction['employee_name']) . '</td>';
        echo '<td class="border border-gray-300 px-4 py-2">' . htmlspecialchars($transaction['group_name']) . '</td>';
        echo '<td class="border border-gray-300 px-4 py-2">' . htmlspecialchars($transaction['transaction_date']) . '</td>';
        echo '<td class="border border-gray-300 px-4 py-2">' . htmlspecialchars($transaction['transaction_type']) . '</td>';
        echo '<td class="border border-gray-300 px-4 py-2">' . htmlspecialchars($transaction['quantity']) . '</td>';
        echo '<td class="border border-gray-300 px-4 py-2">' . htmlspecialchars($transaction['remarks'] ?? '-') . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="8" class="border border-gray-300 px-4 py-2 text-center">No transactions found.</td></tr>';
}
