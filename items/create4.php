<?php
require_once '../config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $item_code = trim($_POST["item_code"]);
    $item_name = trim($_POST["item_name"]);
    $specifications = trim($_POST["specifications"]);
    $image_url = trim($_POST["image_url"]);
    $category_id = intval($_POST["category_id"]);
    $location_id = intval($_POST["location_id"]);
    $quantity = intval($_POST["quantity"]);
    $origin_country = trim($_POST["origin_country"]);
    $warranty_until = date('Y-m-d', strtotime($_POST["warranty_until"]));
    $purchase_date = date('Y-m-d', strtotime($_POST["purchase_date"]));
    $purchase_price = floatval($_POST["purchase_price"]);
    $status = trim($_POST["status"]);
    $created_by = $_SESSION['user_id']; // Assuming you have a session variable for the logged-in user

    // Prepare SQL statement
    $sql = "INSERT INTO items (item_code, item_name, specifications, image_url, category_id, location_id, quantity, origin_country, warranty_until, purchase_date, purchase_price, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssisiiissdsss", $item_code, $item_name, $specifications, $image_url, $category_id, $location_id, $quantity, $origin_country, $warranty_until, $purchase_date, $purchase_price, $status, $created_by);

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        // Item added successfully
        header("Location: items.php?success=Item added successfully");
        exit();
    } else {
        // Error adding item
        echo "Error: " . mysqli_error($conn);
    }

    // Close statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Item</title>
</head>
<body>
    <h2>Add Item</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="item_code">Item Code:</label>
        <input type="text" id="item_code" name="item_code" required><br><br>

        <label for="item_name">Item Name:</label>
        <input type="text" id="item_name" name="item_name" required><br><br>

        <input type="submit" value="Add Item">
    </form>
</body>
</html>