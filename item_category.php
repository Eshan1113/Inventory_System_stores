<?php
require_once 'config.php'; // Ensure the path is correct

// Initialize flags for success and error messages
$mainCategorySuccess = '';
$subCategorySuccess = '';
$mainCategoryError = '';
$subCategoryError = '';
$locationSuccess = '';
$locationError = '';

// Process Main Category Form
if (isset($_POST['main_category_submit'])) {
    $categoryName = sanitizeInput($_POST['main_category_name']);
    $description = sanitizeInput($_POST['description']);

    // Basic validation
    if (empty($categoryName) || empty($description)) {
        $mainCategoryError = "Category Name and Description are required.";
    } else {
        try {
            // Insert the main category into the item_categories table
            $sqlInsertCategory = "INSERT INTO item_categories (category_name, description) VALUES (:category_name, :description)";
            $stmtInsertCategory = $conn->prepare($sqlInsertCategory);
            $stmtInsertCategory->bindParam(':category_name', $categoryName, PDO::PARAM_STR);
            $stmtInsertCategory->bindParam(':description', $description, PDO::PARAM_STR);
            $stmtInsertCategory->execute();

            $mainCategorySuccess = "Main Category added successfully!";
        } catch (PDOException $e) {
            $mainCategoryError = "Error inserting main category: " . $e->getMessage();
        }
    }
}

// Process Sub Category Form
if (isset($_POST['sub_category_submit'])) {
    $subItemName = sanitizeInput($_POST['sub_item_name']);
    $itemNames = isset($_POST['item_names']) ? $_POST['item_names'] : [];

    // Sanitize each item name
    $sanitizedItemNames = array_map('sanitizeInput', $itemNames);

    // Basic validation
    if (empty($subItemName)) {
        $subCategoryError = "Sub Category Name is required.";
    } elseif (empty($sanitizedItemNames) || !array_filter($sanitizedItemNames)) {
        $subCategoryError = "At least one Item Name is required.";
    } else {
        try {
            // Start transaction
            $conn->beginTransaction();

            // Insert sub item and item names as before
            $sqlInsertSub = "INSERT INTO sub_item_list (sub_item_name) VALUES (:sub_item_name)";
            $stmtInsertSub = $conn->prepare($sqlInsertSub);
            $stmtInsertSub->bindParam(':sub_item_name', $subItemName, PDO::PARAM_STR);
            $stmtInsertSub->execute();

            foreach ($sanitizedItemNames as $itemName) {
                if (!empty($itemName)) {
                    $sqlInsertItem = "INSERT INTO item_name_list (item_name) VALUES (:item_name)";
                    $stmtInsertItem = $conn->prepare($sqlInsertItem);
                    $stmtInsertItem->bindParam(':item_name', $itemName, PDO::PARAM_STR);
                    $stmtInsertItem->execute();
                }
            }

            $conn->commit();
            $subCategorySuccess = "Sub Category and Items added successfully!";
        } catch (PDOException $e) {
            $conn->rollBack();
            $subCategoryError = "Error inserting sub category or item names: " . $e->getMessage();
        }
    }
}

// Process Location Form
if (isset($_POST['location_submit'])) {
    $locationName = sanitizeInput($_POST['location_name']);
    $address = sanitizeInput($_POST['address']);

    // Basic validation
    if (empty($locationName) || empty($address)) {
        $locationError = "Location Name and Address are required.";
    } else {
        try {
            // Insert the location into the locations table
            $sqlInsertLocation = "INSERT INTO locations (location_name, address) VALUES (:location_name, :address)";
            $stmtInsertLocation = $conn->prepare($sqlInsertLocation);
            $stmtInsertLocation->bindParam(':location_name', $locationName, PDO::PARAM_STR);
            $stmtInsertLocation->bindParam(':address', $address, PDO::PARAM_STR);
            $stmtInsertLocation->execute();

            $locationSuccess = "Location added successfully!";
        } catch (PDOException $e) {
            $locationError = "Error inserting location: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <link rel="stylesheet" href="css/tailwind.min.css">
    <script src="js/jquery-3.6.0.min.js"></script>
    <title>Item List</title>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include('header1.php'); ?>

    <div class="flex-grow flex justify-around items-start mt-10">
        <!-- Main Category Form -->
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-gray-1000">Create Main Category</h2>

            <!-- Success/Error Message -->
            <?php if (!empty($mainCategorySuccess)): ?>
                <div id="mainSuccessMessage" class="bg-green-100 text-green-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($mainCategorySuccess); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($mainCategoryError)): ?>
                <div id="mainErrorMessage" class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($mainCategoryError); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="item_category.php" method="POST">
                <div>
                    <label for="main_category_name" class="block text-gray-700 font-bold mb-1">Main Category Name</label>
                    <input type="text" name="main_category_name" id="main_category_name" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" value="<?php echo isset($_POST['main_category_name']) ? htmlspecialchars($_POST['main_category_name']) : ''; ?>" required>
                </div>
                <div>
                    <label for="description" class="block text-gray-700 font-bold mb-1">Description</label>
                    <textarea name="description" id="description" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                <input type="hidden" name="main_category_submit" value="1">
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 mt-4">Add Main Category</button>
            </form>
        </div>

        <!-- Sub Category Form -->
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Create Sub Category</h2>

            <!-- Success/Error Message -->
            <?php if (!empty($subCategorySuccess)): ?>
                <div id="subSuccessMessage" class="bg-green-100 text-green-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($subCategorySuccess); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($subCategoryError)): ?>
                <div id="subErrorMessage" class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($subCategoryError); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="item_category.php" method="POST">
                <div>
                    <label for="sub_item_name" class="block text-gray-700 font-bold mb-1">Sub Category Name</label>
                    <input type="text" name="sub_item_name" id="sub_item_name" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" value="<?php echo isset($_POST['sub_item_name']) ? htmlspecialchars($_POST['sub_item_name']) : ''; ?>" required>
                </div>
                <div id="itemsContainer">
                    <label class="block text-gray-700 font-bold mb-1">Add Collection Of Items</label>
                    <div class="item-field flex items-center mb-2">
                        <input type="text" name="item_names[]" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" placeholder="Enter item name" required>
                        <button type="button" class="remove-item ml-2 bg-red-500 text-white px-3 py-1 rounded">Remove</button>
                    </div>
                </div>
                <button type="button" id="addItemButton" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mt-4">Add More Items</button>
                <input type="hidden" name="sub_category_submit" value="1">
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 mt-4">Add Sub Category</button>
            </form>
        </div>

        <!-- Location Form -->
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Create Location</h2>

            <!-- Success/Error Message -->
            <?php if (!empty($locationSuccess)): ?>
                <div id="locationSuccessMessage" class="bg-green-100 text-green-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($locationSuccess); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($locationError)): ?>
                <div id="locationErrorMessage" class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($locationError); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="item_category.php" method="POST">
                <div>
                    <label for="location_name" class="block text-gray-700 font-bold mb-1">Location Name</label>
                    <input type="text" name="location_name" id="location_name" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" value="<?php echo isset($_POST['location_name']) ? htmlspecialchars($_POST['location_name']) : ''; ?>" required>
                </div>
                <div>
                    <label for="address" class="block text-gray-700 font-bold mb-1">Address</label>
                    <textarea name="address" id="address" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>
                <input type="hidden" name="location_submit" value="1">
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 mt-4">Add Location</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('addItemButton').addEventListener('click', function () {
            const itemsContainer = document.getElementById('itemsContainer');
            const itemField = document.createElement('div');
            itemField.className = 'item-field flex items-center mb-2';
            itemField.innerHTML = `
                <input type="text" name="item_names[]" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" placeholder="Enter item name" required>
                <button type="button" class="remove-item ml-2 bg-red-500 text-white px-3 py-1 rounded">Remove</button>
            `;
            itemsContainer.appendChild(itemField);
        });

        document.getElementById('itemsContainer').addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.parentElement.remove();
            }
        });

        // Auto-hide success messages after 3 seconds
        const successMessages = document.querySelectorAll('#mainSuccessMessage, #subSuccessMessage, #locationSuccessMessage');
        successMessages.forEach(msg => {
            setTimeout(() => {
                msg.classList.add('hidden');
            }, 3000);
        });
    </script>
</body>
</html>
