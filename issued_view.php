<?php
// index.php

include 'config.php'; // Include your database connection file

// Function to fetch transactions based on transaction type
function fetchTransactionsByType($conn, $transaction_type = '') {
    // Build condition if transaction type is provided
    $condition = $transaction_type ? "WHERE t.transaction_type = '$transaction_type'" : '';
    
    // NOTE: There's a semicolon in the middle of your SQL in the snippet. 
    //       Make sure your SQL statement is correct. 
    //       Below is a corrected SQL (removed the extra semicolon before $condition).
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
            JOIN items ini ON t.item_id = ini.item_id
            JOIN item_name_list i ON ini.item_name = i.id
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
    
    <!-- Search Transactions -->
    <div class="mb-4">
        <label class="block mb-2 font-medium">Search Transactions</label>
        <input type="text" id="search_transactions" class="block w-full p-2 border rounded" placeholder="Search by item name, employee, group, or remarks...">
    </div>

    <!-- Date Range Filters -->
    <div class="mb-4 grid grid-cols-2 gap-4">
        <div>
            <label for="start_date" class="block font-medium">Start Date</label>
            <input type="date" id="start_date" class="block w-full p-2 border rounded">
        </div>

        <div>
            <label for="end_date" class="block font-medium">End Date</label>
            <input type="date" id="end_date" class="block w-full p-2 border rounded">
        </div>
    </div>

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
    
    <!-- EXPORT TO EXCEL BUTTON -->
    <a href="export2_excel.php" 
       class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
       Export All to Excel
    </a>

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

<!-- AJAX for fetching transactions with filters -->
<script>
    $(document).ready(function () {
        function fetchTransactions() {
            var search = $('#search_transactions').val();
            var transactionType = $('#transaction_type').val();
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();

            $.ajax({
                url: 'issued_fetch_transactions.php',
                method: 'POST',
                data: {
                    search: search,
                    transaction_type: transactionType,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function (response) {
                    $('#transaction_table tbody').html(response);
                },
                error: function () {
                    alert('Failed to fetch transactions. Please try again.');
                }
            });
        }

        // Real-time search
        $('#search_transactions').on('input', fetchTransactions);

        // Handle transaction type change
        $('#transaction_type').change(fetchTransactions);

        // Handle date changes
        $('#start_date, #end_date').on('change', fetchTransactions);
    });
</script>

</body>
</html>
