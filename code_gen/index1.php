<?php
include 'database.php';

$successMessage = "";
$errorMessage = "";
$generatedCode = "";

// Handle form submission for adding a new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name']) && isset($_POST['category_code'])) {
    $categoryName = $_POST['category_name'];
    $categoryCode = $_POST['category_code'];

    // Validate inputs
    if (strlen($categoryCode) === 2 && !empty($categoryName)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, code) VALUES (?, ?)");
        
        try {
            $stmt->execute([$categoryName, $categoryCode]);
            $successMessage = 'Category added successfully';
        } catch(PDOException $e) {
            $errorMessage = 'Category could not be added. Please try again.';
        }
    } else {
        $errorMessage = 'Invalid input. Category name and code are required.';
    }
}

// Handle form submission for adding a new subcategory
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subcategory_name']) && isset($_POST['subcategory_code']) && isset($_POST['category_id'])) {
    $subcategoryName = $_POST['subcategory_name'];
    $subcategoryCode = $_POST['subcategory_code'];
    $categoryId = $_POST['category_id'];

    // Validate inputs
    if (strlen($subcategoryCode) === 2 && !empty($subcategoryName) && !empty($categoryId)) {
        $stmt = $pdo->prepare("INSERT INTO subcategories (name, code, category_id) VALUES (?, ?, ?)");
        
        try {
            $stmt->execute([$subcategoryName, $subcategoryCode, $categoryId]);
            $successMessage = 'Subcategory added successfully';
        } catch(PDOException $e) {
            $errorMessage = 'Subcategory could not be added. Please try again.';
        }
    } else {
        $errorMessage = 'Invalid input. Subcategory name and code are required.';
    }
}

// Handle form submission for generating item code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category']) && isset($_POST['subcategory'])) {
    $categoryId = $_POST['category'];
    $subcategoryId = $_POST['subcategory'];
    $serialNumber = isset($_POST['serial_number']) ? $_POST['serial_number'] : "";

    // Generate item code based on category and subcategory
    $stmt = $pdo->prepare("SELECT code FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT code FROM subcategories WHERE id = ?");
    $stmt->execute([$subcategoryId]);
    $subcategory = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category && $subcategory) {
        $generatedCode = $category['code'] . '-' . $subcategory['code'] . '-' . strtoupper($serialNumber);
    } else {
        $errorMessage = 'Category or Subcategory not found.';
    }
}

// Fetch categories and subcategories for the dropdown
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->query("SELECT * FROM subcategories");
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workshop Item Code Generator</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #4CAF50;
            color: white;
        }
        .error {
            background-color: #f44336;
            color: white;
        }
        .loading {
            display: none;
            padding: 10px;
            background-color: #ffa500;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Workshop Item Code Generator</h1>

        <!-- Category Management Section -->
        <div class="section">
            <h2>Manage Categories</h2>
            <?php if ($successMessage && !isset($_POST['subcategory_name'])): ?>
                <div class="message success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            <?php if ($errorMessage && !isset($_POST['subcategory_name'])): ?>
                <div class="message error"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            <form id="categoryForm" action="" method="post">
                <div class="form-group">
                    <label for="newCategory">New Category Name:</label>
                    <input type="text" id="newCategory" name="category_name" required>
                    
                    <label for="newCategoryCode">Category Code (2 chars):</label>
                    <input type="text" id="newCategoryCode" name="category_code" maxlength="2" required>
                    
                    <button type="submit" class="btn-add">Add Category</button>
                </div>
            </form>
        </div>

        <!-- Subcategory Management Section -->
        <div class="section">
            <h2>Manage Subcategories</h2>
            <?php if ($successMessage && isset($_POST['subcategory_name'])): ?>
                <div class="message success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            <?php if ($errorMessage && isset($_POST['subcategory_name'])): ?>
                <div class="message error"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            <form id="subcategoryForm" action="" method="post">
                <div class="form-group">
                    <label for="categorySelect">Select Category:</label>
                    <select id="categorySelect" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>">
                                <?= htmlspecialchars($category['name']) ?> (<?= htmlspecialchars($category['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label for="newSubcategory">New Subcategory Name:</label>
                    <input type="text" id="newSubcategory" name="subcategory_name" required>
                    
                    <label for="newSubcategoryCode">Subcategory Code (2 chars):</label>
                    <input type="text" id="newSubcategoryCode" name="subcategory_code" maxlength="2" required>
                    
                    <button type="submit" class="btn-add">Add Subcategory</button>
                </div>
            </form>
        </div>

        <!-- Item Code Generation Section -->
        <div class="section">
            <h2>Generate Item Code</h2>
            <div class="loading" id="loadingMessage">Loading...</div>
            <?php if ($successMessage && !isset($_POST['subcategory_name']) && !isset($_POST['category_name'])): ?>
                <div class="message success"><?= htmlspecialchars($generatedCode) ?></div>
            <?php endif; ?>
            <?php if ($errorMessage && !isset($_POST['subcategory_name']) && !isset($_POST['category_name'])): ?>
                <div class="message error"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            <form id="generateForm" action="" method="post" onsubmit="showLoading()">
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>">
                                <?= htmlspecialchars($category['name']) ?> (<?= htmlspecialchars($category['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label for="subcategory">Subcategory:</label>
                    <select id="subcategory" name="subcategory" required>
                        <option value="">Select Subcategory</option>
                        <?php foreach ($subcategories as $subcategory): ?>
                            <option value="<?= htmlspecialchars($subcategory['id']) ?>">
                                <?= htmlspecialchars($subcategory['name']) ?> (<?= htmlspecialchars($subcategory['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label for="serialNumber">Serial Number (Optional):</label>
                    <input type="text" id="serialNumber" name="serial_number" placeholder="Leave blank for auto-generation">
                    
                    <button type="submit" class="btn-generate">Generate Item Code</button>
                </div>
            </form>
        </div>

        <!-- Generated Code Display -->
        <div id="generatedCodeDisplay" class="section">
            <h2>Generated Code</h2>
            <p id="displayCode" class="generated-code"><?= htmlspecialchars($generatedCode) ?></p>
        </div>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loadingMessage').style.display = 'block';
        }
    </script>
</body>
</html>
