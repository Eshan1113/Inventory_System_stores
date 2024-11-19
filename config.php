<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'admin@123');
define('DB_NAME', 'store_management');

// Establish database connection
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check user permissions
function hasPermission($conn, $userId, $permissionName) {
    $stmt = $conn->prepare("CALL check_user_permission(?, ?, @has_permission)");
    $stmt->execute([$userId, $permissionName]);
    
    $result = $conn->query("SELECT @has_permission as has_permission")->fetch();
    return (bool)$result['has_permission'];
}

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to log user activity
function logActivity($conn, $userId, $activityType, $description) {
    $stmt = $conn->prepare("INSERT INTO user_activity_logs (user_id, activity_type, activity_description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $activityType, $description, $_SERVER['REMOTE_ADDR']]);
}
