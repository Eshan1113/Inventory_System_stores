<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);
    $email = sanitizeInput($_POST['email']);
    $full_name = sanitizeInput($_POST['full_name']);
    $role = sanitizeInput($_POST['role']);

    if (empty($username) || empty($password) || empty($email) || empty($full_name) || empty($role)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        $check_query = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = :username OR email = :email");
        $check_query->execute(['username' => $username, 'email' => $email]);
        $result = $check_query->fetch();

        if ($result['count'] > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username or email already exists.']);
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (:username, :password, :email, :full_name, :role)");
        $stmt->execute([
            'username' => $username,
            'password' => $hashed_password,
            'email' => $email,
            'full_name' => $full_name,
            'role' => $role,
        ]);

        echo json_encode(['status' => 'success', 'message' => 'User added successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <style>
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            display: none;
        }
        .message.success {
            color: green;
            background-color: #d4edda;
        }
        .message.error {
            color: red;
            background-color: #f8d7da;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include('header1.php'); ?>

    <div class="container mx-auto p-6">

        <form id="addUserForm" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div id="responseMessage" class="message"></div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Enter username" name="username">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="Enter password" name="password">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="Enter email" name="email">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="full_name">Full Name</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="full_name" type="text" placeholder="Enter full name" name="full_name">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">Role</label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="role" name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button" id="submitUserForm">
                    Add User
                </button>
            </div>
          
        </form>
    </div>

    <script>
   document.addEventListener('DOMContentLoaded', function () {
    const submitUserForm = document.getElementById('submitUserForm');
    const addUserForm = document.getElementById('addUserForm');
    const responseMessage = document.getElementById('responseMessage');

    submitUserForm.addEventListener('click', function () {
        const formData = new FormData(addUserForm);

        fetch('add_user.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Debugging
            responseMessage.style.display = 'block';
            if (data.status === 'success') {
                responseMessage.textContent = data.message;
                responseMessage.className = 'message success';
                addUserForm.reset();
            } else {
                responseMessage.textContent = data.message;
                responseMessage.className = 'message error';
            }
            // Automatically hide the message after 3 seconds
            setTimeout(() => {
                responseMessage.style.display = 'none';
            }, 3000);
        })
        .catch(error => {
            console.error(error); // Log any unexpected errors
            responseMessage.style.display = 'block';
            responseMessage.textContent = 'An unexpected error occurred.';
            responseMessage.className = 'message error';
            // Automatically hide the message after 3 seconds
            setTimeout(() => {
                responseMessage.style.display = 'none';
            }, 3000);
        });
    });
});
;

    </script>
</body>
</html>
