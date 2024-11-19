<?php
// Include the config file for database connection
require_once 'config.php';

// Start the session to verify if the user is logged in
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Define variables and initialize with empty values
$name = $description = $price = $image_url = "";
$name_err = $description_err = $price_err = $image_url_err = "";

// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate price
    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter the price.";
    } elseif (!is_numeric(trim($_POST["price"]))) {
        $price_err = "Please enter a valid number for the price.";
    } else {
        $price = trim($_POST["price"]);
    }

    // Validate image URL
    if (empty(trim($_POST["image_url"]))) {
        $image_url_err = "Please enter an image URL.";
    } else {
        $image_url = trim($_POST["image_url"]);
    }

    // Check for errors before inserting into database
    if (empty($name_err) && empty($description_err) && empty($price_err) && empty($image_url_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO items (name, description, price, image_url) VALUES (?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssds", $param_name, $param_description, $param_price, $param_image_url);

            // Set parameters
            $param_name = $name;
            $param_description = $description;
            $param_price = $price;
            $param_image_url = $image_url;

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to dashboard after success
                header("location: dashboard.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your stylesheet here -->
</head>
<body>
    <div class="container">
        <h2>Add New Item</h2>
        <p>Please fill out this form to add an item to your store.</p>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Name input -->
            <div class="form-group">
                <label for="name">Item Name</label>
                <input type="text" name="name" id="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>

            <!-- Description input -->
            <div class="form-group">
                <label for="description">Item Description</label>
                <textarea name="description" id="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                <span class="invalid-feedback"><?php echo $description_err; ?></span>
            </div>

            <!-- Price input -->
            <div class="form-group">
                <label for="price">Item Price</label>
                <input type="text" name="price" id="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                <span class="invalid-feedback"><?php echo $price_err; ?></span>
            </div>

            <!-- Image URL input -->
            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="text" name="image_url" id="image_url" class="form-control <?php echo (!empty($image_url_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $image_url; ?>">
                <span class="invalid-feedback"><?php echo $image_url_err; ?></span>
            </div>

            <!-- Submit button -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Add Item</button>
            </div>
        </form>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
