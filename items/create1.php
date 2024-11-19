<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_code = $_POST['item_code'];
    $item_name = $_POST['item_name'];
    $specifications = $_POST['specifications'];
    $image_url = $_POST['image_url'];
    $location_id = $_POST['location_id'];
    $category_id = $_POST['category_id'];
    $quantity = $_POST['quantity'];
    $origin_country = $_POST['origin_country'];
    $warranty_until = $_POST['warranty_until'];
    $purchase_date = $_POST['purchase_date'];
    $purchase_price = $_POST['purchase_price'];
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];

    $sql = "INSERT INTO items (item_code, item_name, specifications, image_url, location_id, category_id, quantity, origin_country, warranty_until, purchase_date, purchase_price, status, created_by)
            VALUES ('$item_code', '$item_name', '$specifications', '$image_url', '$location_id', '$category_id', '$quantity', '$origin_country', '$warranty_until', '$purchase_date', '$purchase_price', '$status', '$created_by')";

    if ($conn->query($sql) === TRUE) {
        header("Location: manage_items.php");
        exit();
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch locations and categories for dropdown
$sql_locations = "SELECT * FROM locations";
$result_locations = $conn->query($sql_locations);

$sql_categories = "SELECT * FROM items_category";
$result_categories = $conn->query($sql_categories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="active"><a href="add_item.php">Add Item</a></li>
            <li><a href="add_user.php">Add User</a></li>
            <li><a href="add_category.php">Add Category</a></li>
            <li><a href="add_location.php">Add Location</a></li>
            <li><a href="add_sublocation.php">Add Sub Location</a></li>
            <li><a href="add_employee.php">Add Employee</a></li>
            <li><a href="add_employee_group.php">Add Employee Group</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h2>Add Item</h2>
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <form action="" method="post">
            <label for="item_code">Item Code:</label>
            <input type="text" id="item_code" name="item_code" required>

            <label for="item_name">Item Name:</label>
            <input type="text" id="item_name" name="item_name" required>

            <label for="specifications">Specifications:</label>
            <textarea id="specifications" name="specifications"></textarea>

            <label for="image_url">Image URL:</label>
            <input type="text" id="image_url" name="image_url">

            <label for="location_id">Location:</label>
            <select id="location_id" name="location_id" required>
                <?php while($row = $result_locations->fetch_assoc()) { ?>
                    <option value="<?php echo $row['location_id']; ?>"><?php echo $row['location_name']; ?></option>
                <?php } ?>
            </select>

            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required>
                <?php while($row = $result_categories->fetch_assoc()) { ?>
                    <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                <?php } ?>
            </select>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>

            <label for="origin_country">Origin Country:</label>
            <input type="text" id="origin_country" name="origin_country">

            <label for="warranty_until">Warranty Until:</label>
            <input type="date" id="warranty_until" name="warranty_until">

            <label for="purchase_date">Purchase Date:</label>
            <input type="date" id="purchase_date" name="purchase_date">

            <label for="purchase_price">Purchase Price:</label>
            <input type="number" step="0.01" id="purchase_price" name="purchase_price">

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="available">Available</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>

            <button type="submit">Add Item</button>
        </form>
    </div>
    <footer>
        <p>Admin Panel &copy; 2024</p>
    </footer>
</body>
</html>