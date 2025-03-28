<?php
require_once 'config.php';  // Include your database connection

// Handle errors and exceptions
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Fetch categories and locations
    $categories = $conn->query("SELECT category_id, category_name FROM item_categories")->fetchAll(PDO::FETCH_ASSOC);
    $locations = $conn->query("SELECT location_id, location_name FROM locations")->fetchAll(PDO::FETCH_ASSOC);

    $item = null;
    if (isset($_GET['item_id'])) {
        // Fetch item details if an item_id is provided (for editing)
        $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ?");
        $stmt->execute([$_GET['item_id']]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Collect form data
        $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : null;
        $local_item_code = $_POST['local_item_code'] ?? null;
        $item_name = $_POST['item_name'] ?? null;
        $sub_name = $_POST['sub_name'] ?? null;
        $specifications = $_POST['specifications'] ?? null;
        $category_id = $_POST['category_id'] ?? null;
        $location_id = $_POST['location_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $low_stock_threshold = $_POST['low_stock_threshold'] ?? null;
        $warranty_until = $_POST['warranty_until'] ?? null;
        $purchase_date = $_POST['purchase_date'] ?? null;
        $purchase_price = $_POST['purchase_price'] ?? null;
        $status = $_POST['status'] ?? null;
        $origin_country = $_POST['origin_country'] ?? null;

        echo $item_id . '' . $local_item_code . '' . $item_name . '' . $sub_name . '' . $specifications . '' . $category_id . '' . $location_id . '' . $quantity . '' . $low_stock_threshold . '' . $warranty_until . '' . $purchase_date . '' . $purchase_price . '' . $status . '' . $origin_country . '';
        // exit();


        // Handle image upload
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $upload_dir = 'upload/'; // Set your upload folder path here
            $image_url = $upload_dir . basename($image_name);
            move_uploaded_file($image_tmp_name, $image_url);
        }

        // Validate required fields
        if (empty($local_item_code) || empty($item_name) || empty($category_id) || empty($location_id)) {
            throw new Exception("Please fill all required fields.");
        }

        // Check if item_id exists (to update or insert)
        if ($item_id) {
            // Update existing item
            $stmt = $conn->prepare("UPDATE items SET
                local_item_code = ?,
                item_name = ?,
                sub_name = ?,
                specifications = ?,
                image_url = ?,
                category_id = ?,
                location_id = ?,
                quantity = ?,
                low_stock_threshold = ?,
                warranty_until = ?,
                purchase_date = ?,
                purchase_price = ?,
                status = ?,
                origin_country = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE item_id = ?");
            $stmt->execute([
                $local_item_code,
                $item_name,
                $sub_name,
                $specifications,
                $image_url,
                $category_id,
                $location_id,
                $quantity,
                $low_stock_threshold,
                $warranty_until,
                $purchase_date,
                $purchase_price,
                $status,
                $origin_country,
                $item_id
            ]);
            $response = ['success' => true, 'message' => 'Item updated successfully!'];
        } else {
            // Insert new item
            $stmt = $conn->prepare("INSERT INTO items (local_item_code, item_name, sub_name, specifications, image_url, category_id,
                location_id, quantity, low_stock_threshold, warranty_until, purchase_date, purchase_price, status,
                origin_country, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
            $stmt->execute([
                $local_item_code,
                $item_name,
                $sub_name,
                $specifications,
                $image_url,
                $category_id,
                $location_id,
                $quantity,
                $low_stock_threshold,
                $warranty_until,
                $purchase_date,
                $purchase_price,
                $status,
                $origin_country,
                1  // Assuming created_by is 1
            ]);
            $response = ['success' => true, 'message' => 'Item added successfully!'];
        }

        // Set response message in a session variable
        $_SESSION['response'] = $response;
        header("Location: item_add.php"); // Redirect back to the form
        exit;
    }
} catch (Exception $e) {
    // Log error or display the message
    error_log("Error: " . $e->getMessage());
    $_SESSION['response'] = ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
    header("Location: item_add.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('header.php'); ?>
    <style>
        /* Custom styles for the success and error messages */
        .message {
            display: none;
            padding: 10px;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            background-color: #28a745;
            /* Green for success */
        }

        .error {
            background-color: #dc3545;
            /* Red for error */
        }
    </style>
    <!-- <link href="css/select2.min.css" rel="stylesheet" /> -->

    <!-- Include Select2 JS in the <head> or just before closing </body> tag -->
    <!-- <script src="css/jquery-3.6.0.min.js"></script> -->
    <link rel="stylesheet" href="css/select2.min.css" />
    <script src="css/select2.min.js"></script>

    <!-- <script src="css/select2.min.js"></script> -->
</head>

<body class="bg-gray-50">
    <?php include('header1.php'); ?>

    <div class="container mx-auto p-4">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">Manage Item</h1>

            <?php if (isset($_SESSION['response'])): ?>
                <div id="alert-box"
                    class="fixed top-4 right-4 max-w-sm p-4 rounded-lg shadow-md 
        <?php echo $_SESSION['response']['success'] ? 'bg-green-100 text-green-800 border-green-400' : 'bg-red-100 text-red-800 border-red-400'; ?>"
                    role="alert">
                    <strong>
                        <?php echo $_SESSION['response']['success'] ? 'Success:' : 'Error:'; ?>
                    </strong>
                    <span><?php echo $_SESSION['response']['message']; ?></span>
                </div>

                <script>
                    // Make the alert box disappear after 5 seconds
                    setTimeout(() => {
                        const alertBox = document.getElementById('alert-box');
                        if (alertBox) {
                            alertBox.style.transition = 'opacity 0.5s ease';
                            alertBox.style.opacity = '0';
                            setTimeout(() => alertBox.remove(), 500); // Remove the element after fade-out
                        }
                    }, 3000);
                </script>

                <?php unset($_SESSION['response']); ?>
            <?php endif; ?>

            <form method="POST" action="item_add.php" enctype="multipart/form-data">
                <!-- Hidden field for item_id -->
                <?php if ($item): ?>
                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                <?php endif; ?>

                <!-- Local Item Code -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="local_item_code">Local Item Code</label>
                    <input type="text" name="local_item_code" id="local_item_code"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                        value="<?php echo $item['local_item_code'] ?? ''; ?>">
                </div>

                <!-- Item Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="item_name">Item Name</label>
                    <select name="item_name" id="item_name" class="w-full border rounded select2">
                        <option value="">-- Select Item Name --</option>

                    </select>
                </div>

                <!-- Sub Item Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="sub_name">Sub Item Name</label>
                    <select name="sub_name" id="sub_name" class="w-full border rounded select2">
                        <option value="">-- Select Sub Item Name --</option>
                    </select>
                </div>

                <!-- Specifications -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="specifications">Specifications</label>
                    <textarea name="specifications" id="specifications" rows="6"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="Enter item specifications..."><?= $item['specifications'] ?? '' ?></textarea>
                </div>

                <!-- Image Upload -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="image">Item Image</label>
                    <input type="file" name="image" id="image"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring">
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="w-full border rounded select2">
                        <option value="">-- Select Category --</option>
                        <?php
                        // Define the specific category_ids you want to display
                        $selected_category_ids = [1, 2, 3, 4, 5];

                        // Loop through the categories and display only the ones with category_id 1, 2, 3, 4, or 9
                        foreach ($categories as $category):
                            if (in_array($category['category_id'], $selected_category_ids)): ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php echo isset($item['category_id']) && $item['category_id'] == $category['category_id'] ? 'selected' : ''; ?>>
                                    <?php echo $category['category_name']; ?>
                                </option>
                            <?php endif;
                        endforeach; ?>
                    </select>
                </div>

                <!-- Location -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="location_id">Location</label>
                    <select name="location_id" id="location_id" class="w-full border rounded select2">
                        <option value="">-- Select Location --</option>
                        <?php
                        // Loop through locations and show only those with location_id 1 or 2
                        foreach ($locations as $location):
                            if (in_array($location['location_id'], [1, 2])): ?>
                                <option value="<?php echo $location['location_id']; ?>" <?php echo isset($item['location_id']) && $item['location_id'] == $location['location_id'] ? 'selected' : ''; ?>>
                                    <?php echo $location['location_name']; ?>
                                </option>
                                <?php
                            endif;
                        endforeach;
                        ?>
                    </select>
                </div>
                <!-- Quantity -->
               <!-- Quantity -->
<div class="mb-4">
    <label class="block text-gray-700 font-bold mb-2" for="quantity">Quantity *</label>
    <input type="number" name="quantity" id="quantity" min="0" step="1"
        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
        value="<?= htmlspecialchars($item['quantity'] ?? '0') ?>" required>
</div>

<!-- Low Stock Threshold -->
<div class="mb-4">
    <label class="block text-gray-700 font-bold mb-2" for="low_stock_threshold">Low Stock Threshold *</label>
    <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0" step="1"
        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
        value="<?= htmlspecialchars($item['low_stock_threshold'] ?? '0') ?>" required>
</div>

                <!-- Warranty Date -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="warranty_date">Warranty Until</label>
                    <input type="date" name="warranty_date" id="warranty_date"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                        value="<?= $item['warranty_until'] ?? '' ?>">
                </div>

                <!-- Purchase Date -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="purchase_date">Purchase Date</label>
                    <input type="date" name="purchase_date" id="purchase_date"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                        value="<?= $item['purchase_date'] ?? '' ?>">
                </div>

                <!-- Purchase Price -->
            <!-- Purchase Price -->
<div class="mb-4">
    <label class="block text-gray-700 font-bold mb-2" for="purchase_price">Purchase Price *</label>
    <input type="number" name="purchase_price" id="purchase_price" min="0" step="0.01" required
        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
        value="<?= htmlspecialchars($item['purchase_price'] ?? '0.00') ?>">
</div>


                <!-- Origin Country -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="origin_country">Origin Country</label>
                    <input type="text" name="origin_country" id="origin_country"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                        value="<?= $item['origin_country'] ?? '' ?>">
                </div>

                <!-- Submit Button -->
                <div class="mb-4">
                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring">
                        Save Item
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>


        $('#sub_name').select2({
            placeholder: '-- Select Sub Item Name --',
            allowClear: true,
            ajax: {
                url: 'fetch_subitems.php', // Your backend URL to fetch sub-item names
                dataType: 'json',
                method: 'GET',
                delay: 250, // Delay for input for better performance
                processResults: function (data) {
                    // The 'data' is expected to be an array or object of the form {results: [...]}
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,               // The option's value
                                text: item.sub_item_name   // The option's label
                            };
                        })
                    };
                }
            }
        });
        $('#item_name').select2({
            placeholder: '-- Select Sub Item Name --',
            allowClear: true,
            ajax: {
                url: 'fetch_n_items.php', // Your backend URL to fetch sub-item names
                dataType: 'json',
                method: 'GET',
                delay: 250, // Delay for input for better performance
                processResults: function (data) {
                    // The 'data' is expected to be an array or object of the form {results: [...]}
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,               // The option's value
                                text: item.item_name   // The option's label
                            };
                        })
                    };
                }
            }
        });


        // Fetch sub-items based on selected item (optional if you want to load sub-items directly based on item selection)
        $('#item_name').on('change', function () {
            var item_id = $(this).val();
            if (item_id) {
                $('#sub_name').prop('disabled', false); // Enable sub-item dropdown if item selected
            } else {
                $('#sub_name').prop('disabled', true); // Disable sub-item dropdown if no item selected
            }
        });


    </script>
</body>

</html>