<?php
require_once 'config.php';

$stmt = $conn->prepare("SELECT * FROM users ORDER BY user_id");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1 class="text-2xl font-bold mb-4">View Users</h1>
        
        <!-- <div class="mb-4">
            <input type="text" id="searchUser" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Search for users by username or email">
        </div> -->
        
        <div id="responseMessage" class="message"></div>

        <table class="table-auto w-full bg-white shadow-md rounded">
            <thead>
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Username</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Full Name</th>
                    <th class="border px-4 py-2">Role</th>
               
                </tr>
            </thead>
            <tbody id="userTableBody">
                <?php foreach ($users as $user): ?>
                <tr id="userRow_<?php echo $user['user_id']; ?>">
                    <td class="border px-4 py-2"><?php echo $user['user_id']; ?></td>
                    <td class="border px-4 py-2"><?php echo $user['username']; ?></td>
                    <td class="border px-4 py-2"><?php echo $user['email']; ?></td>
                    <td class="border px-4 py-2"><?php echo $user['full_name']; ?></td>
                    <td class="border px-4 py-2"><?php echo $user['role']; ?></td>
                   
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchUser = document.getElementById('searchUser');
            const userTableBody = document.getElementById('userTableBody');
            const responseMessage = document.getElementById('responseMessage');

            searchUser.addEventListener('input', function () {
                const query = searchUser.value.trim();

                fetch(`search_user.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        userTableBody.innerHTML = data.users.map(user => `
                            <tr id="userRow_${user.user_id}">
                                <td class="border px-4 py-2">${user.user_id}</td>
                                <td class="border px-4 py-2">${user.username}</td>
                                <td class="border px-4 py-2">${user.email}</td>
                                <td class="border px-4 py-2">${user.full_name}</td>
                                <td class="border px-4 py-2">${user.role}</td>
                               
                            </tr>
                        `).join('');
                        attachDeleteEvents();
                    })
                    .catch(error => console.error(error));
            });

            function attachDeleteEvents() {
                const deleteButtons = document.querySelectorAll('.deleteUserBtn');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const userId = button.dataset.id;

                        fetch('delete_user.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: userId }),
                        })
                            .then(response => response.json())
                            .then(data => {
                                responseMessage.style.display = 'block';
                                if (data.status === 'success') {
                                    responseMessage.textContent = data.message;
                                    responseMessage.className = 'message success';
                                    document.getElementById(`userRow_${userId}`).remove();
                                } else {
                                    responseMessage.textContent = data.message;
                                    responseMessage.className = 'message error';
                                }
                                setTimeout(() => {
                                    responseMessage.style.display = 'none';
                                }, 3000);
                            })
                            .catch(error => {
                                console.error(error);
                                responseMessage.style.display = 'block';
                                responseMessage.textContent = 'An unexpected error occurred.';
                                responseMessage.className = 'message error';
                                setTimeout(() => {
                                    responseMessage.style.display = 'none';
                                }, 3000);
                            });
                    });
                });
            }

            attachDeleteEvents();
        });
    </script>
</body>
</html>
