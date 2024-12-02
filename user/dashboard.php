<?php
require_once '../config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}


// Fetch quick statistics
$stats = [
 
    'total_items' => $conn->query("SELECT COUNT(*) FROM items")->fetchColumn(),
    'low_stock' => $conn->query("SELECT COUNT(*) FROM items WHERE quantity <= 5")->fetchColumn(),
    'total_employees' => $conn->query("SELECT COUNT(*) FROM employees WHERE status = 'active'")->fetchColumn(),
    'recent_transactions' => $conn->query("SELECT COUNT(*) FROM item_transactions WHERE transaction_date  >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetchColumn()
];

// Fetch low stock items (those with 5 or fewer in stock)
$lowStockItems = $conn->query("SELECT item_name, quantity FROM items WHERE quantity <= 5")->fetchAll();

// Fetch recent transactions
$recentTransactions = $conn->query("
    SELECT it.*, inl.item_name AS item_name, e.full_name AS employee_name
    FROM item_transactions it
    JOIN items i ON it.item_id = i.item_id
    JOIN item_name_list inl ON i.item_name = inl.id
    JOIN employees e ON it.employee_id = e.employee_id
    ORDER BY it.transaction_date DESC
    LIMIT 5
")->fetchAll();
$lowStockItems = $conn->query("
    SELECT inl.item_name, i.quantity, i.low_stock_threshold
    FROM items i
    JOIN item_name_list inl ON i.item_name = inl.id
    WHERE i.quantity <= i.low_stock_threshold
")->fetchAll();

?>

<?php include('header.php'); ?>

<body class="bg-gray-50">
    <?php include('header1.php'); ?>

    <!-- Main Content Area -->
    <div class="p-6 space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-4 gap-6">
            <!-- Total Items -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                        <i class="fas fa-box text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Items</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total_items']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-500 bg-opacity-10">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['low_stock']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Active Employees -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                        <i class="fas fa-users text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Employees</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total_employees']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-500 bg-opacity-10">
                        <i class="fas fa-exchange-alt text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">24h Transactions</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['recent_transactions']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions and Recent Transactions -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-4">
                    <?php if (hasPermission($conn, $_SESSION['user_id'], 'create_item')): ?>
                    <a href="item_add.php
                    " class="quick-action flex items-center p-4 bg-blue-50 rounded-lg">
                        <i class="fas fa-plus-circle text-blue-500 text-xl mr-3"></i>
                        <span class="text-blue-700">New Item</span>
                    </a>
                    <?php endif; ?>

                    <?php if (hasPermission($conn, $_SESSION['user_id'], 'manage_transactions')): ?>
                    <a href="issued_create.php" class="quick-action flex items-center p-4 bg-green-50 rounded-lg">
                        <i class="fas fa-exchange-alt text-green-500 text-xl mr-3"></i>
                        <span class="text-green-700">New Transaction</span>
                    </a>
                    <?php endif; ?>

                    <!-- <?php if (hasPermission($conn, $_SESSION['user_id'], 'create_employee')): ?>
                    <a href="employees/create.php" class="quick-action flex items-center p-4 bg-purple-50 rounded-lg">
                        <i class="fas fa-user-plus text-purple-500 text-xl mr-3"></i>
                        <span class="text-purple-700">Add Employee</span>
                    </a>
                    <?php endif; ?> -->

                    <a href="reports.php" class="quick-action flex items-center p-4 bg-yellow-50 rounded-lg">
                        <i class="fas fa-chart-bar text-yellow-500 text-xl mr-3"></i>
                        <span class="text-yellow-700">View Reports</span>
                    </a>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Recent Transactions</h2>
                <div class="overflow-hidden">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
    <?php foreach ($recentTransactions as $transaction): ?>
    <tr class="table-row">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <?php echo htmlspecialchars($transaction['item_name']); // Correct item name ?>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <?php echo htmlspecialchars($transaction['employee_name']); ?>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                <?php echo $transaction['transaction_type'] == 'issue' 
                    ? 'bg-red-100 text-red-800' 
                    : 'bg-green-100 text-green-800'; ?>">
                <?php echo ucfirst($transaction['transaction_type']); ?>
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <?php echo date('M d, H:i', strtotime($transaction['transaction_date'])); ?>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>

                    </table>
                </div>
            </div>

            <!-- Low Stock Items List -->
            <div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Low Stock Items</h2>
    <div class="overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($lowStockItems as $item): ?>
                <tr class="table-row">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($item['item_name']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo $item['quantity']; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

        </div>
    </div>
    <footer class=" text-black py-3 mt-3 text-center">
        <p>&copy; 2024 Developed by DT. All Rights Reserved.</p>
    </footer>
</body>
</html>
