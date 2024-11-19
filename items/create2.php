<?php
require_once '../config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST['item_name']);
    $category = trim($_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $unit_price = (float)$_POST['unit_price'];
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    
    try {
        $stmt = $conn->prepare("INSERT INTO items (item_name, category, quantity, unit_price, location, description) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$item_name, $category, $quantity, $unit_price, $location, $description]);
        $success_message = "Item added successfully!";
    } catch(PDOException $e) {
        $error_message = "Error adding item: " . $e->getMessage();
    }
}

// Fetch categories for dropdown
$categories = $conn->query("SELECT DISTINCT category FROM items")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item - Store Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-button {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            transition: transform 0.2s;
        }
        .gradient-button:hover {
            transform: translateY(-2px);
        }
        .form-card {
            background: linear-gradient(135deg, #ffffff 0%, #f3f4f6 100%);
        }
        .input-field {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .input-field:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .success-animation {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gray-800 hover:text-indigo-600">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold text-gray-800">Add New Item</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">
                        <i class="fas fa-user mr-2"></i>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto p-6">
        <?php if (isset($success_message)): ?>
        <div class="success-animation bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <?php echo $success_message; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <?php echo $error_message; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Add Item Form -->
        <div class="form-card rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>
                Add New Item
            </h2>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-6">
                <!-- Item Name -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-tag text-indigo-600 mr-2"></i>Item Name
                    </label>
                    <input type="text" name="item_name" required 
                           class="input-field w-full px-4 py-2 rounded-md bg-white border focus:outline-none"
                           placeholder="Enter item name">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-folder text-indigo-600 mr-2"></i>Category
                    </label>
                    <select name="category" required 
                            class="input-field w-full px-4 py-2 rounded-md bg-white border focus:outline-none">
                        <option value="">Select Category</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="new">+ Add New Category</option>
                    </select>
                </div>

                <!-- Quantity and Price Row -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-cubes text-indigo-600 mr-2"></i>Quantity
                        </label>
                        <input type="number" name="quantity" required min="0"
                               class="input-field w-full px-4 py-2 rounded-md bg-white border focus:outline-none"
                               placeholder="Enter quantity">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-dollar-sign text-indigo-600 mr-2"></i>Unit Price
                        </label>
                        <input type="number" name="unit_price" required step="0.01" min="0"
                               class="input-field w-full px-4 py-2 rounded-md bg-white border focus:outline-none"
                               placeholder="Enter unit price">
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>Location
                    </label>
                    <input type="text" name="location" required
                           class="input-field w-full px-4 py-2 rounded-md bg-white border focus:outline-none"
                           placeholder="Enter storage location">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-align-left text-indigo-600 mr-2"></i>Description
                    </label>
                    <textarea name="description" rows="4"
                              class="input-field w-full px-4 py-2 rounded-md bg-white border focus:outline-none"
                              placeholder="Enter item description"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <button type="reset" class="px-6 py-2 rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </button>
                    <button type="submit" class="gradient-button text-white px-6 py-2 rounded-md shadow-md hover:shadow-lg focus:outline-none">
                        <i class="fas fa-plus-circle mr-2"></i>Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle category selection
        const categorySelect = document.querySelector('select[name="category"]');
        const originalOptions = categorySelect.innerHTML;
        
        categorySelect.addEventListener('change', function() {
            if (this.value === 'new') {
                const newCategory = prompt('Enter new category name:');
                if (newCategory) {
                    const option = document.createElement('option');
                    option.value = newCategory;
                    option.text = newCategory;
                    this.innerHTML = originalOptions;
                    this.add(option, 1);
                    this.value = newCategory;
                } else {
                    this.value = '';
                }
            }
        });

        // Animate success message
        const successMessage = document.querySelector('.success-animation');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        }
    });
    </script>
</body>
</html>
