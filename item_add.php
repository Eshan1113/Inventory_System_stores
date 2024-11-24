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
    $local_item_code = $_POST['local_item_code'];
    $item_name = $_POST['item_name'];
    $specifications = $_POST['specifications'];
    $category_id = $_POST['category_id'];
    $location_id = $_POST['location_id'];
    $quantity = $_POST['quantity'];
    $low_stock_threshold = $_POST['low_stock_threshold']; // New field
    $warranty_until = $_POST['warranty_date'];
    $purchase_date = $_POST['purchase_date'];
    $purchase_price = $_POST['purchase_price'];
    $status = $_POST['status'];

    // Handle image upload
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = 'upload/'; // Set your upload folder path here
        $image_url = $upload_dir . basename($image_name);
        move_uploaded_file($image_tmp_name, $image_url);
    }

    $origin_country = $_POST['origin_country']; // Capture the user-entered country

if ($item_id) {
    // Update existing item
    $stmt = $conn->prepare("UPDATE items SET 
        local_item_code = ?, 
        item_name = ?, 
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

    $stmt->execute([$local_item_code, $item_name, $specifications, $image_url, $category_id, $location_id,
                    $quantity, $low_stock_threshold, $warranty_until, $purchase_date, $purchase_price, 
                    $status, $origin_country, $item_id]);
} else {
    // Insert new item
    $stmt = $conn->prepare("INSERT INTO items (local_item_code, item_name, specifications, image_url, category_id, 
        location_id, quantity, low_stock_threshold, warranty_until, purchase_date, purchase_price, status, 
        origin_country, created_by, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");

    $stmt->execute([$local_item_code, $item_name, $specifications, $image_url, $category_id, $location_id, 
                    $quantity, $low_stock_threshold, $warranty_until, $purchase_date, $purchase_price, 
                    $status, $origin_country, 1]);  // Assuming created_by is 1
}


    // Redirect or message after successful insert/update
    header('Location: item_add.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <script src="css/jquery-3.6.0.min.js"></script>
    <script src="css/select2.min.js"></script>
    <script src="css/sweetalert.min.js"></script>
</head>
<body class="bg-gray-50">
<?php include('header1.php'); ?>

<div class="container mx-auto p-4">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Manage Item</h1>
        
        <form id="itemForm" method="POST" action="item_add.php" enctype="multipart/form-data">
            <!-- Hidden field for item_id -->
            <?php if ($item): ?>
                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
            <?php endif; ?>

            <!-- Local Item Code -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="local_item_code">Local Item Code</label>
                <input type="text" name="local_item_code" id="local_item_code" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="<?php echo $item['local_item_code'] ?? ''; ?>" required>
            </div>
            <!-- Item Code -->
         
            <!-- Item Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="item_name">Item Name</label>
                <input type="text" name="item_name" id="item_name" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="<?php echo $item['item_name'] ?? ''; ?>" required>
            </div>
            <div class="mb-4">
    <label class="block text-gray-700 font-bold mb-2" for="specifications">Specifications</label>
    <textarea name="specifications" id="specifications" rows="6" 
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
              placeholder="Enter item specifications..."><?= $item['specifications'] ?? '' ?></textarea>
</div>
        <br>

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

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="low_stock_threshold">Low Stock Threshold</label>
                <input type="number" name="low_stock_threshold" id="low_stock_threshold"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="<?php echo $item['low_stock_threshold'] ?? ''; ?>" required>
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
            <div class="mb-4">
    <label class="block text-gray-700 font-bold mb-2" for="origin_country">Origin Country</label>
    <input 
        type="text" 
        name="origin_country" 
        id="origin_country" 
        value="<?php echo htmlspecialchars($item['origin_country'] ?? ''); ?>" 
        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" 
        placeholder="Enter origin country" 
        required
    >
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
            <button type="submit" id="submitBtn" class="w-full bg-blue-500 text-white py-2 px-4 rounded focus:outline-none focus:ring">Save Item</button>
        </form>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#itemForm').on('submit', function (event) {
            event.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function (response) {
                    let res = JSON.parse(response);
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                        }).then(() => {
                            window.location.href = 'item_add.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message,
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred while saving the item.',
                    });
                }
            });
        });
    });
</script>
</body>
</html>

