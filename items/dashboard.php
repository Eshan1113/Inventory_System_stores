<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Fetch quick statistics
$stats = [
    'total_items' => $conn->query("SELECT COUNT(*) FROM items")->fetchColumn(),
    'low_stock' => $conn->query("SELECT COUNT(*) FROM items WHERE status = 'low_stock'")->fetchColumn(),
    'total_employees' => $conn->query("SELECT COUNT(*) FROM employees WHERE status = 'active'")->fetchColumn(),
    'recent_transactions' => $conn->query("SELECT COUNT(*) FROM item_transactions WHERE transaction_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetchColumn()
];

// Fetch recent transactions
$recentTransactions = $conn->query("
    SELECT it.*, i.item_name, e.full_name as employee_name 
    FROM item_transactions it
    JOIN items i ON it.item_id = i.item_id
    JOIN employees e ON it.employee_id = e.employee_id
    ORDER BY transaction_date DESC 
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Store Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        .sidebar-link {
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
        }
        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .stat-card {
            transition: transform 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .quick-action {
            transition: all 0.2s ease;
        }
        .quick-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .table-row {
            transition: background-color 0.2s ease;
        }
        .table-row:hover {
            background-color: rgba(243, 244, 246, 0.5);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-blue-700 to-blue-600 text-white shadow-xl">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-8">Store Management</h2>
                <ul class="space-y-6">
                    <!-- Item Details -->
                    <li>
                        <div class="mb-2 text-lg font-semibold text-gray-100">
                            <i class="fas fa-box-open mr-2"></i> Item Details
                        </div>
                        <ul class="ml-6 space-y-2">
                            <li><a href="items/create.php" class="sidebar-link block text-gray-200 hover:text-white">Add Item</a></li>
                            <li><a href="items/view.php" class="sidebar-link block text-gray-200 hover:text-white">View Items</a></li>
                            <li><a href="items/edit.php" class="sidebar-link block text-gray-200 hover:text-white">Edit Item</a></li>
                            <li><a href="items/delete.php" class="sidebar-link block text-gray-200 hover:text-white">Delete Item</a></li>
                        </ul>
                    </li>

                    <!-- Transaction Details -->
                    <li>
                        <div class="mb-2 text-lg font-semibold text-gray-100">
                            <i class="fas fa-exchange-alt mr-2"></i> Transactions
                        </div>
                        <ul class="ml-6 space-y-2">
                            <li><a href="transactions/create.php" class="sidebar-link block text-gray-200 hover:text-white">Add Transaction</a></li>
                            <li><a href="transactions/view.php" class="sidebar-link block text-gray-200 hover:text-white">View Transactions</a></li>
                            <li><a href="transactions/edit.php" class="sidebar-link block text-gray-200 hover:text-white">Edit Transaction</a></li>
                            <li><a href="transactions/delete.php" class="sidebar-link block text-gray-200 hover:text-white">Delete Transaction</a></li>
                        </ul>
                    </li>

                    <!-- User Management -->
                    <li>
                        <div class="mb-2 text-lg font-semibold text-gray-100">
                            <i class="fas fa-users mr-2"></i> User Management
                        </div>
                        <ul class="ml-6 space-y-2">
                            <li><a href="users/create.php" class="sidebar-link block text-gray-200 hover:text-white">Add User</a></li>
                            <li><a href="users/view.php" class="sidebar-link block text-gray-200 hover:text-white">View Users</a></li>
                            <li><a href="users/edit.php" class="sidebar-link block text-gray-200 hover:text-white">Edit User</a></li>
                            <li><a href="users/delete.php" class="sidebar-link block text-gray-200 hover:text-white">Delete User</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Navigation -->
            <nav class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="flex justify-between items-center py-4">
                        <h1 class="text-xl font-bold text-gray-800">Dashboard Overview</h1>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <a href="logout.php" class="flex items-center text-red-600 hover:text-red-700">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

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
                            <a href="items/create.php" class="quick-action flex items-center p-4 bg-blue-50 rounded-lg">
                                <i class="fas fa-plus-circle text-blue-500 text-xl mr-3"></i>
                                <span class="text-blue-700">New Item</span>
                            </a>
                            <?php endif; ?>

                            <?php if (hasPermission($conn, $_SESSION['user_id'], 'manage_transactions')): ?>
                            <a href="transactions/create.php" class="quick-action flex items-center p-4 bg-green-50 rounded-lg">
                                <i class="fas fa-exchange-alt text-green-500 text-xl mr-3"></i>
                                <span class="text-green-700">New Transaction</span>
                            </a>
                            <?php endif; ?>

                            <?php if (hasPermission($conn, $_SESSION['user_id'], 'create_employee')): ?>
                            <a href="employees/create.php" class="quick-action flex items-center p-4 bg-purple-50 rounded-lg">
                                <i class="fas fa-user-plus text-purple-500 text-xl mr-3"></i>
                                <span class="text-purple-700">Add Employee</span>
                            </a>
                            <?php endif; ?>

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
                                            <?php echo htmlspecialchars($transaction['item_name']); ?>
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
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add smooth scrolling behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                