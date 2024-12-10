
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
                            <li><a href="item_add.php" class="sidebar-link block text-gray-200 hover:text-white">Add Item</a></li>
                            <li><a href="Item_viwe.php" class="sidebar-link block text-gray-200 hover:text-white">View Items</a></li>
                            <li><a href="code_gen/index.php" class="sidebar-link block text-gray-200 hover:text-white">Genarte Item Code</a></li>
                            <!-- <li><a href="items/edit.php" class="sidebar-link block text-gray-200 hover:text-white">Edit Item</a></li>
                            <li><a href="items/delete.php" class="sidebar-link block text-gray-200 hover:text-white">Delete Item</a></li> -->
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
                    <li>
                        <div class="mb-2 text-lg font-semibold text-gray-100">
                            <i class="fas fa-users mr-2"></i> User Management
                        </div>
                        <ul class="ml-6 space-y-2">
                            <li><a href="add_user.php" class="sidebar-link block text-gray-200 hover:text-white">Add User</a></li>
                            <li><a href="view_user.php" class="sidebar-link block text-gray-200 hover:text-white">View Users</a></li>
                            
                        </ul>
                    </li>
                    <li>
                        <div class="mb-2 text-lg font-semibold text-gray-100">
                        <i class="fa fa-cog fa-spin fa-1x fa-fw"></i>
                        <span class="sr-only">Loading...</span>Login Setting
                        </div>
                        <ul class="ml-6 space-y-2">
                            <li><a href="user_log.php" class="sidebar-link block text-gray-200 hover:text-white">User Log</a></li>
                            <li><a href="password_change.php" class="sidebar-link block text-gray-200 hover:text-white">Change Password</a></li>
                            
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="flex-1">
            <!-- Top Navigation -->
            <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Dashboard Title with Image -->
            <div class="flex items-center space-x-2">
                <img src="nw.png" alt="Dashboard" class="w-55 h-10">
               
            </div>
            
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="flex items-center text-red-600 hover:text-red-700">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>
