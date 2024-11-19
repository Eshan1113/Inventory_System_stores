<?php
include 'config.php'; // Include your database connection file

// Function to fetch transactions based on transaction type
function fetchTransactionsByType($conn, $transaction_type = '') {
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
    return $result->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch all transactions initially
$transactions = fetchTransactionsByType($conn);
?>

<?php include('header.php'); ?>

<script>
    $(document).ready(function() {
        // Handle transaction type change
        $('#transaction_type').change(function() {
            var transactionType = $(this).val();
            $.ajax({
                url: 'issued_fetch_transactions.php', // AJAX endpoint
                method: 'POST',
                data: { transaction_type: transactionType },
                success: function(response) {
                    $('#transaction_table tbody').html(response); // Update table body
                },
                error: function() {
                    alert('Failed to fetch transactions. Please try again.');
                }
            });
        });
    });
</script>
</head>
<body class="bg-gray-50">
<?php include('header1.php'); ?>
    <br>
    <div class="max-w-5xl mx-auto bg-white p-10 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-4">Item Transactions</h2>

        <!-- Dropdown for transaction types -->
        <label class="block mb-2 font-medium">Transaction Type</label>
        <select id="transaction_type" class="block w-full p-2 border rounded">
            <option value="">Select Transaction Type</option>
            <option value="issue">Issue</option>
            <option value="return">Return</option>
            <option value="damage">Damage</option>
            <option value="lost">Lost</option>
            <option value="discard">Discard</option>
        </select>

        <br>
        <table class="table-auto w-full border-collapse border border-gray-200" id="transaction_table">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">#</th>
                    <th class="border border-gray-300 px-4 py-2">Item Name</th>
                    <th class="border border-gray-300 px-4 py-2">Employee</th>
                    <th class="border border-gray-300 px-4 py-2">Group</th>
                    <th class="border border-gray-300 px-4 py-2">Transaction Date</th>
                    <th class="border border-gray-300 px-4 py-2">Type</th>
                    <th class="border border-gray-300 px-4 py-2">Quantity</th>
                    <th class="border border-gray-300 px-4 py-2">Remarks</th>
                 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $index => $transaction): ?>
                    <tr class="<?= $index % 2 === 0 ? 'bg-gray-100' : ''; ?>">
                        <td class="border border-gray-300 px-4 py-2"><?= $index + 1; ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['item_name']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['employee_name']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['group_name']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['transaction_date']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['transaction_type']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['quantity']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['remarks'] ?? '-'); ?></td>
                   
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
