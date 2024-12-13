<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Update last login
        $stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        
        // Log activity
        logActivity($conn, $user['user_id'], 'LOGIN', 'User logged in');
        
        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: dashboard.php");
        } elseif ($user['role'] === 'user') {
            header("Location: user/dashboard.php");
        } else {
            // Default fallback
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Management - Login</title>
    <link href="css/tailwind.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <style>
        .video-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -1;
    animation: fadeInVideo 2s ease-in-out; /* Add fade-in animation */
}

@keyframes fadeInVideo {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

        /* Make the background video fill the entire screen */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        /* Darken overlay for the video */
        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Black overlay with 50% opacity */
            z-index: 0;
        }

        /* Styling for login container with frosted glass effect */
        .login-container {
            z-index: 10;
            position: relative;
            max-width: 400px; /* Optional, you can adjust the size */
            width: 100%;
            background: rgba(255, 255, 255, 0.1); /* Semi-transparent background */
            border-radius: 10px;
            padding: 20px;
            backdrop-filter: blur(10px); /* Frosted glass effect */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
            border: 1px solid rgba(255, 255, 255, 0.3); /* Light border */
        }

        /* Container styling for the entire body */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5); /* Optional, to darken the background */
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-header h1 {
            font-size: 1.5rem;
            color: #fff;
        }

        /* Style for labels (make text color white) */
        label {
            color: #fff; /* Set the text color of the labels to white */
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Background Video -->
    <video autoplay muted loop class="video-background">
        <source src="new.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Darken Overlay -->
    <div class="video-overlay"></div>

    <div class="min-h-screen flex items-center justify-center">
        <div class="login-container">
            <!-- Logo Section -->
            <div class="form-header">
                <center><img src="nw.png" alt="Logo" class="w-45 h-30 mb-3"></center>
                <h1 class="text-white font-bold">Store Management</h1>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2" for="username">
                        Username
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="username" type="text" name="username" required>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="password" type="password" name="password" required>
                </div>
                
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full"
        type="submit">
    Sign In
</button>
<br>
<br>
<a href="viweuser_item.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full block text-center">
    View Item
</a>
                <footer class="text-white py-3 mt-3 text-center">
                    <p>&copy; 2024 Developed by DT. All Rights Reserved.</p>
                </footer>
            </form>
        </div>
    </div>
</body>
</html>
