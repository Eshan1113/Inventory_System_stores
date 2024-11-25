<?php
require_once 'config.php'; // Include the database connection file

// Check if the user is logged in
if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$message = ""; // To store success or error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = sanitizeInput($_POST['current_password']);
    $new_password = sanitizeInput($_POST['new_password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);
    $user_id = $_SESSION['user_id'];

    // Fetch current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $message = "Current password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        if ($update_stmt->execute([$hashed_password, $user_id])) {
            $message = "Password updated successfully!";
        } else {
            $message = "Failed to update the password. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('header.php'); ?>
    <title>Change Password</title>
    <style>
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .relative {
    position: relative;
}

.absolute {
    position: absolute;
}

.right-3 {
    right: 12px; /* Adjust spacing as needed */
}

.top-1/2 {
    top: 50%;
}

.transform {
    transform: translateY(-50%);
}

.cursor-pointer {
    cursor: pointer;
}

    </style>
</head>
<body class="bg-gray-50">
    <?php include('header1.php'); ?>
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md mx-auto mt-10">
        <h2 class="text-2xl font-bold mb-4 text-center">Change Password</h2>

        <?php if (!empty($message)) : ?>
            <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form id="password-change-form" action="" method="POST">
        <div class="mb-4">
    <label for="current_password" class="block text-gray-700">Current Password</label>
    <div class="relative">
        <input 
            type="password" 
            id="current_password" 
            name="current_password" 
            required 
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
        >
        <span 
            class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer" 
            onclick="togglePasswordVisibility('current_password', this)"
        >
            👁️
        </span>
    </div>
</div>

<div class="mb-4">
    <label for="new_password" class="block text-gray-700">New Password</label>
    <div class="relative">
        <input 
            type="password" 
            id="new_password" 
            name="new_password" 
            required 
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
        >
        <span 
            class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer" 
            onclick="togglePasswordVisibility('new_password', this)"
        >
            👁️
        </span>
    </div>
</div>

<div class="mb-4">
    <label for="confirm_password" class="block text-gray-700">Confirm New Password</label>
    <div class="relative">
        <input 
            type="password" 
            id="confirm_password" 
            name="confirm_password" 
            required 
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
        >
        <span 
            class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer" 
            onclick="togglePasswordVisibility('confirm_password', this)"
        >
            👁️
        </span>
    </div>
</div>

            <button 
                type="submit" 
                class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600"
            >
                Update Password
            </button>
        </form>
    </div>
</body>
</html>
<Script>
    function togglePasswordVisibility(fieldId, iconElement) {
    const field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        iconElement.textContent = "🙈"; // Change to a "hide" icon
    } else {
        field.type = "password";
        iconElement.textContent = "👁️"; // Change back to "show" icon
    }
}

    </Script>