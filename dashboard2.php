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
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-600 text-white shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Dashboard Menu</h2>
            <ul>
                <!-- Item Details Submenu -->
                <li class="mb-4">
                    <a href="#" class="flex items-center text-white hover:text-gray-200">
                        <i class="fas fa-box-open mr-3"></i> Item Details
                    </a>
                    <ul class="pl-6">
                        <li><a href="items/create.php" class="text-white hover:text-gray-200">Add Item</a></li>
                        <li><a href="items/view.php" class="text-white hover:text-gray-200">View Items</a></li>
                        <li><a href="items/edit.php" class="text-white hover:text-gray-200">Edit Item</a></li>
                        <li><a href="items/delete.php" class="text-white hover:text-gray-200">Delete Item</a></li>
                    </ul>
                </li>
                
                <!-- Transaction Details Submenu -->
                <li class="mb-4">
                    <a href="#" class="flex items-center text-white hover:text-gray-200">
                        <i class="fas fa-exchange-alt mr-3"></i> Transaction Details
                    </a>
                    <ul class="pl-6">
                        <li><a href="transactions/create.php" class="text-white hover:text-gray-200">Add Transaction</a></li>
                        <li><a href="transactions/view.php" class="text-white hover:text-gray-200">View Transactions</a></li>
                        <li><a href="transactions/edit.php" class="text-white hover:text-gray-200">Edit Transaction</a></li>
                        <li><a href="transactions/delete.php" class="text-white hover:text-gray-200">Delete Transaction</a></li>
                    </ul>
                </li>

                <!-- User Details Submenu -->
                <li class="mb-4">
                    <a href="#" class="flex items-center text-white hover:text-gray-200">
                        <i class="fas fa-users mr-3"></i> User Details
                    </a>
                    <ul class="pl-6">
                        <li><a href="users/create.php" class="text-white hover:text-gray-200">Add User</a></li>
                        <li><a href="users/view.php" class="text-white hover:text-gray-200">View Users</a></li>
                        <li><a href="users/edit.php" class="text-white hover:text-gray-200">Edit User</a></li>
                        <li><a href="users/delete.php" class="text-white hover:text-gray-200">Delete User</a></li>
                    </ul>
                </li>

                <!-- Category and Location Details Submenu -->
                <li class="mb-4">
                    <a href="#" class="flex items-center text-white hover:text-gray-200">
                        <i class="fas fa-cogs mr-3"></i> Category & Location Details
                    </a>
                    <ul class="pl-6">
                        <li><a href="categories_locations/create.php" class="text-white hover:text-gray-200">Add Category</a></li>
                        <li><a href="categories_locations/view.php" class="text-white hover:text-gray-200">View Categories</a></li>
                        <li><a href="categories_locations/edit.php" class="text-white hover:text-gray-200">Edit Category</a></li>
                        <li><a href="categories_locations/delete.php" class="text-white hover:text-gray-200">Delete Category</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-1 bg-gray-100 p-6">
            <!-- Navigation -->
            <nav class="bg-blue-600 text-white shadow-lg mb-6">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="flex justify-between items-center py-4">
                        <div class="flex items-center">
                            <span class="text-xl font-bold">Store Management</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <a href="logout.php" class="hover:text-gray-200">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Items Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                            <i class="fas fa-box text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-gray-600">Total Items</p>
                            <p class="text-lg font-semibold text-gray-700"><?php echo $stats['total_items']; ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Low Stock Items Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75">
                            <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-gray-600">Low Stock Items</p>
                            <p class="text-lg font-semibold text-gray-700"><?php echo $stats['low_stock']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Active Employees Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-gray-600">Active Employees</p>
                            <p class="text-lg font-semibold text-gray-700"><?php echo $stats['total_employees']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- 24h Transactions Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500 bg-opacity-75">
                            <i class="fas fa-exchange-alt text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-gray-600">24h Transactions</p>
                            <p class="text-lg font-semibold text-gray-700"><?php echo $stats['recent_transactions']; ?></p>
                        </div>
                    </div>

    <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <?php if (hasPermission($conn, $_SESSION['user_id'], 'create_item')): ?>
                        <a href="items/create.php" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100">
                            <i class="fas fa-plus-circle text-blue-500 mr-3"></i>
                            <span>New Item</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (hasPermission($conn, $_SESSION['user_id'], 'manage_transactions')): ?>
                        <a href="transactions/create.php" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100">
                            <i class="fas fa-exchange-alt text-green-500 mr-3"></i>
                            <span>New Transaction</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (hasPermission($conn, $_SESSION['user_id'], 'create_employee')): ?>
                        <a href="employees/create.php" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100">
                            <i class="fas fa-user-plus text-purple-500 mr-3"></i>
                            <span>Add Employee</span>
                        </a>
                        <?php endif; ?>
                        
                        <a href="reports.php" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100">
                            <i class="fas fa-chart-bar text-yellow-500 mr-3"></i>
                            <span>View Reports</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Recent Transactions</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Item</th>
                                    <th class="text-left py-2">Employee</th>
                                    <th class="text-left py-2">Type</th>
                                    <th class="text-left py-2">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $transaction): ?>
                                <tr class="border-b">
                                    <td class="py-2"><?php echo htmlspecialchars($transaction['item_name']); ?></td>
                                    <td class="py-2"><?php echo htmlspecialchars($transaction['employee_name']); ?></td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            <?php echo $transaction['transaction_type'] == 'issue' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo ucfirst($transaction['transaction_type']); ?>
                                        </span>
                                    </td>
                                    <td class="py-2"><?php echo date('M d, H:i', strtotime($transaction['transaction_date'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>