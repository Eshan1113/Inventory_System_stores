<?php
// Enable error reporting for debugging (already handled in config.php)

// Include the configuration file (which includes sanitizeInput)
require_once 'config.php'; // Ensure the path is correct

// Initialize flags for success and error messages
$success = false;
$error = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize POST data using the function from config.php
    $subItemName = isset($_POST['sub_item_name']) ? sanitizeInput($_POST['sub_item_name']) : '';
    $itemNames = isset($_POST['item_names']) ? $_POST['item_names'] : [];

    // Sanitize each item name
    $sanitizedItemNames = array_map('sanitizeInput', $itemNames);

    // Basic validation
    if (empty($subItemName)) {
        $error = "Main Category Name is required.";
    } elseif (empty($sanitizedItemNames) || !array_filter($sanitizedItemNames)) { // Ensure at least one item is provided
        $error = "At least one Item Name is required.";
    } else {
        try {
            // Start transaction to ensure all inserts succeed
            $conn->beginTransaction();

            // Check if sub_item_name already exists
            $sqlCheck = "SELECT id FROM sub_item_list WHERE sub_item_name = :sub_item_name";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bindParam(':sub_item_name', $subItemName, PDO::PARAM_STR);
            $stmtCheck->execute();

            if ($stmtCheck->rowCount() > 0) {
                // Sub-item exists, get its ID
                $subItem = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                $subItemId = $subItem['id'];
            } else {
                // Insert new sub-item
                $sqlInsertSub = "INSERT INTO sub_item_list (sub_item_name) VALUES (:sub_item_name)";
                $stmtInsertSub = $conn->prepare($sqlInsertSub);
                $stmtInsertSub->bindParam(':sub_item_name', $subItemName, PDO::PARAM_STR);
                $stmtInsertSub->execute();
                $subItemId = $conn->lastInsertId();
            }

            // Prepare the SELECT statement to check for existing item_names (without checking id)
            $sqlCheckItem = "SELECT id FROM item_name_list WHERE item_name = :item_name";
            $stmtCheckItem = $conn->prepare($sqlCheckItem);

            // Prepare the INSERT statement for new item_names (only inserting item_name)
            $sqlInsertItem = "INSERT INTO item_name_list (item_name) VALUES (:item_name)";
            $stmtInsertItem = $conn->prepare($sqlInsertItem);

            // Insert each item name into item_name_list
            foreach ($sanitizedItemNames as $itemName) {
                if (!empty($itemName)) { // Skip empty item names
                    // Check if the item_name already exists
                    $stmtCheckItem->bindParam(':item_name', $itemName, PDO::PARAM_STR);
                    $stmtCheckItem->execute();

                    if ($stmtCheckItem->rowCount() === 0) {
                        // Item name does not exist, proceed to insert
                        $stmtInsertItem->bindParam(':item_name', $itemName, PDO::PARAM_STR);
                        $stmtInsertItem->execute();
                    }
                }
            }

            // Commit the transaction
            $conn->commit();
            $success = true;
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $conn->rollBack();
            $error = "Error inserting data: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <link rel="stylesheet" href="css/tailwind.min.css"> <!-- Correctly linked as a stylesheet -->
    <script src="js/jquery-3.6.0.min.js"></script> <!-- Ensure the path is correct -->
    <title>Item List</title>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include('header1.php'); ?>

    <!-- Main Content Area -->
    <br>
    <br>
    <div class="flex-grow flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Crate New Item Category</h2>

            <!-- Success Message -->
            <?php if ($success): ?>
                <div id="successMessage" class="bg-green-100 text-green-700 p-4 rounded mb-4">
                    Form submitted successfully!
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form id="itemCategoryForm" action="item_category.php" method="POST" class="space-y-4">
                
                <!-- Sub Item Name -->
                <div>
                    <label for="sub_item_name" class="block text-gray-700 font-bold mb-1">Main Category Name</label>
                    <input type="text" name="sub_item_name" id="sub_item_name" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" placeholder="Enter main category name" required>
                </div>

                <!-- Item Names -->
                <div id="itemsContainer">
                    <label class="block text-gray-700 font-bold mb-1">Item Names</label>
                    <div class="item-field flex items-center mb-2">
                        <input type="text" name="item_names[]" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" placeholder="Enter item name" required>
                        <button type="button" class="remove-item ml-2 bg-red-500 text-white px-3 py-1 rounded">Remove</button>
                    </div>
                </div>

                <!-- Add More Items Button -->
                <button type="button" id="addItemButton" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Add More Items</button>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 focus:outline-none focus:ring mt-4">Submit</button>
            </form>
        </div>
    </div>

    <!-- JavaScript to Add/Remove Item Fields and Auto-Hide Success Message -->
    <script>
        document.getElementById('addItemButton').addEventListener('click', function() {
            const itemsContainer = document.getElementById('itemsContainer');
            const itemField = document.createElement('div');
            itemField.className = 'item-field flex items-center mb-2';
            itemField.innerHTML = `
                <input type="text" name="item_names[]" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" placeholder="Enter item name" required>
                <button type="button" class="remove-item ml-2 bg-red-500 text-white px-3 py-1 rounded">Remove</button>
            `;
            itemsContainer.appendChild(itemField);
        });

        // Event delegation for removing item fields
        document.getElementById('itemsContainer').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.parentElement.remove();
            }
        });

        // Auto-hide the success message after 3 seconds
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            setTimeout(() => {
                successMessage.classList.add('hidden');
            }, 3000); // 3000 milliseconds = 3 seconds
        }
    </script>
</body>
</html>
