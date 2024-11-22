<?php
// Include the database configuration
require_once 'config.php';

// Fetch user activity logs
try {
    // Replace 'user_id' with the correct column name for user ID in your 'users' table
    $stmt = $conn->prepare("
        SELECT 
            logs.log_id,
            logs.user_id,
            users.username AS user_name,
            logs.activity_type,
            logs.activity_description,
            logs.ip_address,
            logs.activity_timestamp
        FROM user_activity_logs AS logs
        INNER JOIN users ON logs.user_id = users.user_id  -- Adjusted column name
        ORDER BY logs.activity_timestamp DESC
    ");
    $stmt->execute();
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching logs: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include('header.php'); ?>
</head>
<body class="bg-gray-50">
    <?php include('header1.php'); ?>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">User Activity Logs</h1>
        <div class="overflow-x-auto">
            <table class="table-auto w-full bg-white shadow rounded-lg">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2">Log ID</th>
                        <th class="px-4 py-2">User ID</th>
                        <th class="px-4 py-2">Username</th>
                        <th class="px-4 py-2">Activity Type</th>
                        <th class="px-4 py-2">Description</th>
                        <th class="px-4 py-2">IP Address</th>
                        <th class="px-4 py-2">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="border-t">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['log_id']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['user_id']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['user_name']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['activity_type']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['activity_description']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['activity_timestamp']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">No activity logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
