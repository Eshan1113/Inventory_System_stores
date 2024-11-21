<?php
require_once 'config.php';

// Fetch categories and locations
$categories = $conn->query("SELECT category_id, category_name FROM item_categories")->fetchAll(PDO::FETCH_ASSOC);
$locations = $conn->query("SELECT location_id, location_name FROM locations")->fetchAll(PDO::FETCH_ASSOC);

$item = null;
if (isset($_GET['item_id'])) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ?");
    $stmt->execute([$_GET['item_id']]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : null;
    $item_code = $_POST['item_code'];
    $item_name = $_POST['item_name'];
    $specifications = $_POST['specifications'];
    $category_id = $_POST['category_id'];
    $location_id = $_POST['location_id'];
    $quantity = $_POST['quantity'];
    $warranty_until = $_POST['warranty_date'];  // Updated column name
    $purchase_date = $_POST['purchase_date'];
    $purchase_price = $_POST['purchase_price'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = 'upload/';  // Set your upload folder path here
        $image_url = $upload_dir . basename($image_name);
        move_uploaded_file($image_tmp_name, $image_url);
    }

    if ($item_id) {
        // Update existing item
        $stmt = $conn->prepare("UPDATE items SET 
            item_code = ?, 
            item_name = ?, 
            specifications = ?, 
            image_url = ?, 
            category_id = ?, 
            location_id = ?, 
            quantity = ?, 
            warranty_until = ?,  // Updated column name
            purchase_date = ?, 
            purchase_price = ?, 
            status = ?, 
            updated_at = CURRENT_TIMESTAMP 
            WHERE item_id = ?");
        
        $stmt->execute([$item_code, $item_name, $specifications, $image_url, $category_id, $location_id, 
                        $quantity, $warranty_until, $purchase_date, $purchase_price, $status, $item_id]);
    } else {
        // Insert new item
        $stmt = $conn->prepare("INSERT INTO items (item_code, item_name, specifications, image_url, category_id, 
            location_id, quantity, warranty_until, purchase_date, purchase_price, status, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");

        $stmt->execute([$item_code, $item_name, $specifications, $image_url, $category_id, $location_id, 
                        $quantity, $warranty_until, $purchase_date, $purchase_price, $status, 1]);  // Assuming created_by is 1
    }

    // Redirect or message after successful insert/update
    header('Location: items_list.php');  // Redirect to item list page or wherever necessary
    exit;
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
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Manage Item</h1>
        
        <form method="POST" action="item_add.php" enctype="multipart/form-data">
            <!-- Hidden field for item_id -->
            <?php if ($item): ?>
                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
            <?php endif; ?>

            <!-- Item Code -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="item_code">Item Code</label>
                <input type="text" name="item_code" id="item_code" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="<?php echo $item['item_code'] ?? ''; ?>" required>
            </div>

            <!-- Item Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="item_name">Item Name</label>
                <input type="text" name="item_name" id="item_name" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="<?php echo $item['item_name'] ?? ''; ?>" required>
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
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>" 
                                <?php echo isset($item['category_id']) && $item['category_id'] == $category['category_id'] ? 'selected' : ''; ?>>
                            <?php echo $category['category_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Location -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="location_id">Location</label>
                <select name="location_id" id="location_id" class="w-full border rounded select2">
                    <option value="">-- Select Location --</option>
                    <?php foreach ($locations as $location): ?>
                        <option value="<?php echo $location['location_id']; ?>" 
                                <?php echo isset($item['location_id']) && $item['location_id'] == $location['location_id'] ? 'selected' : ''; ?>>
                            <?php echo $location['location_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Quantity -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="quantity">Stock</label>
                <input type="number" name="quantity" id="quantity" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="<?php echo $item['quantity'] ?? '0'; ?>" required>
            </div>

            <!-- Warranty Until -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="warranty_date">Warranty Date</label>
                <input type="date" name="warranty_date" id="warranty_date" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="<?php echo $item['warranty_until'] ?? ''; ?>">
            </div>

            <!-- Purchase Date -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="purchase_date">Purchase Date</label>
                <input type="date" name="purchase_date" id="purchase_date" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="<?php echo $item['purchase_date'] ?? ''; ?>">
            </div>

            <!-- Purchase Price -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="purchase_price">Purchase Price (in Rupees)</label>
                <input type="number" name="purchase_price" id="purchase_price" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="<?php echo $item['purchase_price'] ?? ''; ?>">
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="status">Status</label>
                <select name="status" id="status" class="w-full border rounded select2">
                    <option value="">-- Select Status --</option>
                    <option value="available" <?php echo isset($item['status']) && $item['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="low_stock" <?php echo isset($item['status']) && $item['status'] == 'low_stock' ? 'selected' : ''; ?>>Low Stock</option>
                    <option value="out_of_stock" <?php echo isset($item['status']) && $item['status'] == 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded focus:outline-none focus:ring">Save Item</button>
        </form>
    </div>
</div>

</body>
</html>
