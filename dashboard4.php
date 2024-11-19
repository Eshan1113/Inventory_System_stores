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


import React from 'react';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Menu, LogOut, Box, AlertTriangle, Users, RefreshCcw, PlusCircle, BarChart2, UserPlus } from 'lucide-react';

const Dashboard = () => {
  // Sample data to mimic PHP variables
  const stats = {
    total_items: 150,
    low_stock: 12,
    total_employees: 45,
    recent_transactions: 23
  };

  const recentTransactions = [
    { item_name: "Laptop", employee_name: "John Doe", transaction_type: "issue", transaction_date: "2024-03-17 14:30:00" },
    { item_name: "Monitor", employee_name: "Jane Smith", transaction_type: "return", transaction_date: "2024-03-17 13:15:00" },
    { item_name: "Keyboard", employee_name: "Mike Johnson", transaction_type: "issue", transaction_date: "2024-03-17 12:00:00" },
  ];

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Top Navigation */}
      <nav className="bg-blue-600 text-white shadow-lg">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex justify-between items-center py-4">
            <div className="flex items-center space-x-4">
              <Menu className="h-6 w-6 md:hidden" />
              <span className="text-xl font-bold">Store Management</span>
            </div>
            <div className="flex items-center space-x-4">
              <span className="hidden md:inline">Welcome, Admin</span>
              <LogOut className="h-5 w-5 cursor-pointer" />
            </div>
          </div>
        </div>
      </nav>

      <div className="flex">
        {/* Sidebar - Hidden on mobile */}
        <div className="hidden md:block w-64 bg-blue-600 text-white min-h-screen p-6">
          <h2 className="text-2xl font-bold mb-6">Dashboard Menu</h2>
          <div className="space-y-6">
            {/* Menu Items */}
            <div>
              <div className="flex items-center mb-2 text-white hover:text-gray-200 cursor-pointer">
                <Box className="mr-2 h-5 w-5" />
                <span>Item Details</span>
              </div>
              <div className="ml-6 space-y-2">
                <div className="text-white hover:text-gray-200 cursor-pointer">Add Item</div>
                <div className="text-white hover:text-gray-200 cursor-pointer">View Items</div>
                <div className="text-white hover:text-gray-200 cursor-pointer">Edit Item</div>
                <div className="text-white hover:text-gray-200 cursor-pointer">Delete Item</div>
              </div>
            </div>
          </div>
        </div>

        {/* Main Content */}
        <div className="flex-1 p-6">
          {/* Statistics Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <Card>
              <CardContent className="pt-6">
                <div className="flex items-center">
                  <div className="p-3 rounded-full bg-blue-500 bg-opacity-75">
                    <Box className="h-6 w-6 text-white" />
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-600">Total Items</p>
                    <p className="text-lg font-semibold text-gray-700">{stats.total_items}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardContent className="pt-6">
                <div className="flex items-center">
                  <div className="p-3 rounded-full bg-yellow-500 bg-opacity-75">
                    <AlertTriangle className="h-6 w-6 text-white" />
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-600">Low Stock</p>
                    <p className="text-lg font-semibold text-gray-700">{stats.low_stock}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardContent className="pt-6">
                <div className="flex items-center">
                  <div className="p-3 rounded-full bg-green-500 bg-opacity-75">
                    <Users className="h-6 w-6 text-white" />
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-600">Active Employees</p>
                    <p className="text-lg font-semibold text-gray-700">{stats.total_employees}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardContent className="pt-6">
                <div className="flex items-center">
                  <div className="p-3 rounded-full bg-purple-500 bg-opacity-75">
                    <RefreshCcw className="h-6 w-6 text-white" />
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-600">24h Transactions</p>
                    <p className="text-lg font-semibold text-gray-700">{stats.recent_transactions}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Quick Actions and Recent Transactions */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Quick Actions */}
            <Card>
              <CardHeader>
                <CardTitle>Quick Actions</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div className="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 cursor-pointer">
                    <PlusCircle className="h-5 w-5 text-blue-500 mr-3" />
                    <span>New Item</span>
                  </div>
                  <div className="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 cursor-pointer">
                    <RefreshCcw className="h-5 w-5 text-green-500 mr-3" />
                    <span>New Transaction</span>
                  </div>
                  <div className="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 cursor-pointer">
                    <UserPlus className="h-5 w-5 text-purple-500 mr-3" />
                    <span>Add Employee</span>
                  </div>
                  <div className="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 cursor-pointer">
                    <BarChart2 className="h-5 w-5 text-yellow-500 mr-3" />
                    <span>View Reports</span>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Recent Transactions */}
            <Card>
              <CardHeader>
                <CardTitle>Recent Transactions</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="overflow-x-auto">
                  <table className="min-w-full">
                    <thead>
                      <tr className="border-b">
                        <th className="text-left py-2">Item</th>
                        <th className="text-left py-2">Employee</th>
                        <th className="text-left py-2">Type</th>
                        <th className="text-left py-2">Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      {recentTransactions.map((transaction, index) => (
                        <tr key={index} className="border-b">
                          <td className="py-2">{transaction.item_name}</td>
                          <td className="py-2">{transaction.employee_name}</td>
                          <td className="py-2">
                            <span className={`px-2 py-1 rounded-full text-xs ${
                              transaction.transaction_type === 'issue' 
                                ? 'bg-red-100 text-red-800' 
                                : 'bg-green-100 text-green-800'
                            }`}>
                              {transaction.transaction_type.charAt(0).toUpperCase() + transaction.transaction_type.slice(1)}
                            </span>
                          </td>
                          <td className="py-2">
                            {new Date(transaction.transaction_date).toLocaleString('en-US', {
                              month: 'short',
                              day: 'numeric',
                              hour: '2-digit',
                              minute: '2-digit'
                            })}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
