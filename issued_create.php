<?php
include 'config.php'; // Include your database connection file

// Fetch data function using prepared statements
function fetchData($conn, $table, $columns, $condition = '', $params = []) {
    $sql = "SELECT $columns FROM $table $condition";
    $stmt = $conn->prepare($sql);
    
    // Bind parameters dynamically
    foreach ($params as $param => $value) {
        $stmt->bindValue($param, $value);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch items and employee groups
$items = fetchData($conn, 'items i', 'i.item_id, n.item_name', 'JOIN item_name_list n ON i.item_name = n.id');
$employee_groups = fetchData($conn, 'employee_groups', 'group_id, group_name');

// Fetch employees dynamically based on selected group
$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : '';
$employees = [];
if ($group_id) {
    $employees = fetchData($conn, 'employees', 'employee_id, full_name', "WHERE group_id = :group_id", [':group_id' => $group_id]);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect or show an error
    die("User is not logged in.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_transaction'])) {
    $item_id = $_POST['item_id'];
    $group_id = $_POST['group_id'];
    $employee_id = $_POST['employee_id'];
    $transaction_type = $_POST['transaction_type'];
    $transaction_date = $_POST['transaction_date'];
    $quantity = $_POST['quantity'];
    $remarks = $_POST['remarks'] ?? null;
    $user_id = $_SESSION['user_id']; // This will be used as 'created_by'

    // Handle update query based on transaction type
    $update_query = '';
    if (in_array($transaction_type, ['issue', 'damage', 'lost', 'discard'])) {
        $update_query = "UPDATE items SET quantity = quantity - :quantity WHERE item_id = :item_id AND created_by = :created_by";
    } elseif ($transaction_type === 'return') {
        $update_query = "UPDATE items SET quantity = quantity + :quantity WHERE item_id = :item_id AND created_by = :created_by";
    }
    
    if ($update_query) {
        $stmt = $conn->prepare($update_query);
        $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT); // Use $user_id here
    
        if ($stmt->execute()) {
            // Insert the transaction into the database
            $stmt = $conn->prepare("INSERT INTO item_transactions (item_id, employee_id, transaction_date, transaction_type, quantity, remarks, created_by) 
                                    VALUES (:item_id, :employee_id, :transaction_date, :transaction_type, :quantity, :remarks, :created_by)");
    
            // Bind the values for the insert query
            $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->bindValue(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt->bindValue(':transaction_date', $transaction_date, PDO::PARAM_STR);
            $stmt->bindValue(':transaction_type', $transaction_type, PDO::PARAM_STR);
            $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':remarks', $remarks, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT); // Use $user_id here
    
            if ($stmt->execute()) {
                $success_message = "Transaction recorded successfully!";
            } else {
                $error_message = "Error recording transaction: " . implode(', ', $stmt->errorInfo());
            }
        } else {
            $error_message = "Error updating item quantity: " . implode(', ', $conn->errorInfo());
        }
    } else {
        $error_message = "Invalid transaction type.";
    }
}
?>



<?php include('header.php'); ?>

<script>
    $(document).ready(function() {
        // Initialize Select2 for Item, Group, and Employee dropdowns
        $('#item_id').select2();
        $('#group_id').select2();
        $('#employee_id').select2();

        // If there's a success message, automatically hide it after 3 seconds
        if ($('#success-message').length) {
            setTimeout(function() {
                $('#success-message').fadeOut();
            }, 3000); // 3000ms = 3 seconds
        }
    });
</script>
</head>
<body class="bg-gray-50">
<?php
include('header1.php');
?>
    <br>
    <div class="max-w-3xl mx-auto bg-white p-10 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-4">Item Transaction</h2>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-500 text-green-700 p-4 mb-4 rounded">
                <?= $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-500 text-red-700 p-4 mb-4 rounded">
                <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <!-- Item Name (Searchable Dropdown) -->
         

            <!-- Group Name (Searchable Dropdown) -->
            <label class="block mt-4 mb-2 font-medium">Group Name</label>
            <select name="group_id" id="group_id" class="block w-full p-2 border rounded" onchange="this.form.submit()">
                <option value="">Select Group</option>
                <?php foreach ($employee_groups as $group): ?>
                    <option value="<?= $group['group_id']; ?>" <?= $group_id == $group['group_id'] ? 'selected' : ''; ?>>
                        <?= $group['group_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Employee Name (Searchable Dropdown) -->
            <label class="block mt-4 mb-2 font-medium">Employee Name</label>
            <select name="employee_id" id="employee_id" class="block w-full p-2 border rounded" required>
                <option value="">Select Employee</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= $employee['employee_id']; ?>"><?= $employee['full_name']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <label class="block mb-2 font-medium">Item Name</label>
            <select name="item_id" id="item_id" class="block w-full p-2 border rounded" required>
                <option value="">Select Item</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?= $item['item_id']; ?>"><?= $item['item_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <!-- Transaction Type -->
            <label class="block mt-4 mb-2 font-medium">Transaction Type</label>
            <select name="transaction_type" class="block w-full p-2 border rounded" required>
                <option value="">Select Type</option>
                <option value="issue">Issue</option>
                <option value="return">Return</option>
                <option value="damage">Damage</option>
                <option value="lost">Lost</option>
                <option value="discard">Discard</option>
            </select>

            <!-- Transaction Date -->
            <label class="block mt-4 mb-2 font-medium">Transaction Date</label>
            <input type="datetime-local" name="transaction_date" class="block w-full p-2 border rounded" required>

            <!-- Quantity -->
            <label class="block mt-4 mb-2 font-medium">Quantity</label>
            <input type="number" name="quantity" class="block w-full p-2 border rounded" required min="1">

            <!-- Remarks -->
            <label class="block mt-4 mb-2 font-medium">Remarks</label>
            <textarea name="remarks" class="block w-full p-2 border rounded"></textarea>

            <!-- Submit Button -->
            <button type="submit" name="submit_transaction" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded">Submit Transaction</button>
        </form>
    </div>
</body>
</html>
