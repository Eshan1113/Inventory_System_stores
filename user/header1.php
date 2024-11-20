
<div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-blue-700 to-blue-600 text-white shadow-xl">
            <div class="p-6">
            <h2 class="text-2xl font-bold mb-8">
  <a href="dashboard.php">Store Management</a>
</h2>
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
                            <li><a href="issued_create.php" class="sidebar-link block text-gray-200 hover:text-white">Add Transaction</a></li>
                            <li><a href="issued_view.php" class="sidebar-link block text-gray-200 hover:text-white">View Transactions</a></li>
                            <!-- <li><a href="issued_items/edit.php" class="sidebar-link block text-gray-200 hover:text-white">Edit Transaction</a></li> -->
                            <li><a href="issued_delete.php" class="sidebar-link block text-gray-200 hover:text-white">Delete Transaction</a></li>
                        </ul>
                    </li>

                    <!-- User Management -->
                   
                </ul>
            </div>
        </div>
        <div class="flex-1">
            <!-- Top Navigation -->
            <nav class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="flex justify-between items-center py-4">
                        <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <a href="logout.php" class="flex items-center text-red-600 hover:text-red-700">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </nav>