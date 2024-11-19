<?php
// items/index.php - List all items
require_once '../config.php';

if (!isLoggedIn() || !hasPermission($conn, $_SESSION['user_id'], 'view_item')) {
    header("Location: ../login.php");
    exit();
}

// Handle delete action
if (isset($_POST['delete']) && hasPermission($conn, $_SESSION['user_id'], 'delete_item')) {
    $itemId = filter_var($_POST['item_id'], FILTER_VALIDATE_INT);
    if ($itemId) {
        try {
            $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
            $stmt->execute([$itemId]);
            logActivity($conn, $_SESSION['user_id'], 'DELETE', "Deleted item ID: $itemId");
            $_SESSION['success'] = "Item deleted successfully";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Unable to delete item. It may be referenced in transactions.";
        }
    }
}

// Fetch all items with search and pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = '';
$params = [];

if ($search) {
    $whereClause = "WHERE item_name LIKE ? OR item_code LIKE ? OR specifications LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM items $whereClause");
$stmt->execute($params);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

$stmt = $conn->prepare("
    SELECT i.*, l.location_name
    FROM items i
    LEFT JOIN locations l ON i.`Item Location` = l.location_id
    $whereClause
    ORDER BY i.item_id DESC
    LIMIT ? OFFSET ?
");
$params[] = $limit;
$params[] = $offset;
$stmt->execute($params);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Management - Store Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <?php include '../includes/navigation.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Item Management</h1>
            <?php if (hasPermission($conn, $_SESSION['user_id'], 'create_item')): ?>
            <a href="create.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus"></i> Add New Item
            </a>
            <?php endif; ?>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search items..." 
                           class="w-full px-4 py-2 border rounded-lg">
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo htmlspecialchars($item['item_code']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo htmlspecialchars($item['item_name']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo htmlspecialchars($item['location_name']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo htmlspecialchars($item['quantity']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php
                                switch ($item['status']) {
                                    case 'available':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'low_stock':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'out_of_stock':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                }
                                ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $item['status'])); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="view.php?id=<?php echo $item['item_id']; ?>" 
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if (hasPermission($conn, $_SESSION['user_id'], 'edit_item')): ?>
                            <a href="edit.php?id=<?php echo $item['item_id']; ?>" 
                               class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (hasPermission($conn, $_SESSION['user_id'], 'delete_item')): ?>
                            <button onclick="confirmDelete(<?php echo $item['item_id']; ?>)" 
                                    class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" 
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium"><?php echo $offset + 1; ?></span>
                            to
                            <span class="font-medium"><?php echo min($offset + $limit, $totalItems); ?></span>
                            of
                            <span class="font-medium"><?php echo $totalItems; ?></span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 <?php echo $page === $i ? 'bg-blue-50' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function confirmDelete(itemId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="item_id" value="${itemId}">
                    <input type="hidden" name="delete" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    </script>
</body>
</html>
