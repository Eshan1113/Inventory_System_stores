<?php
include '../config.php';

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$transaction_type = isset($_POST['transaction_type']) ? trim($_POST['transaction_type']) : '';
$start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
$end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';

$conditions = [];
$params = [];

if (!empty($transaction_type)) {
    $conditions[] = "t.transaction_type = :transaction_type";
    $params[':transaction_type'] = $transaction_type;
}
if (!empty($search)) {
    $conditions[] = "(i.item_name LIKE :search OR e.full_name LIKE :search OR g.group_name LIKE :search OR t.remarks LIKE :search)";
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

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

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

$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>
