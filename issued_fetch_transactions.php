<?php
include 'config.php';

// Fetch transactions based on type
if (isset($_POST['transaction_type'])) {
    $transaction_type = $_POST['transaction_type'];
    $condition = $transaction_type ? "WHERE t.transaction_type = '$transaction_type'" : '';
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
            $condition
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
        echo '<tr><td colspan="9" class="border border-gray-300 px-4 py-2 text-center">No transactions found for this type.</td></tr>';
    }
}
