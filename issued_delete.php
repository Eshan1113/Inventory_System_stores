<?php
include 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = $_POST['transaction_id'] ?? null;

    if ($transaction_id) {
        // Delete the transaction
        $stmt = $conn->prepare("DELETE FROM item_transactions WHERE transaction_id = ?");
        $stmt->bindValue(1, $transaction_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Transaction deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete transaction.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid transaction ID.']);
    }
    exit;
}

// Fetch items based on transaction type
if (isset($_GET['transaction_type'])) {
    $transaction_type = $_GET['transaction_type'];

    // Join with the items table to fetch item names
    $stmt = $conn->prepare("
        SELECT it.transaction_id, it.item_id, it.quantity, i.item_name 
        FROM item_transactions it
        JOIN items i ON it.item_id = i.item_id
        WHERE it.transaction_type = ?
    ");
    $stmt->bindValue(1, $transaction_type, PDO::PARAM_STR);
    $stmt->execute();

    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($transactions);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Issued Item</title>
    <link href="css/tailwind.min.css" rel="stylesheet">
    <link href="css/select2.min.css" rel="stylesheet" />
    <script src="css/jquery-3.6.0.min.js"></script>
    <script src="css/select2.min.js"></script>
    <script src="css/sweetalert.min.js"></script>
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .form-select,
        .form-button {
            width: 100%;
            padding: 0.75rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
        }
    </style>
    <?php include('header.php'); ?>
</head>
<body class="bg-gray-50">
<?php include('header1.php'); ?>
<br>
    <div class="form-container">
        <h2 class="text-2xl font-bold mb-4 text-center">Delete Issued Item</h2>

        <!-- Transaction Type Dropdown -->
        <label for="transaction_type" class="form-label">Transaction Type:</label>
        <select id="transaction_type" class="form-select bg-gray-100 border border-gray-300">
            <option value="">Select Type</option>
            <option value="issue">Issue</option>
            <option value="return">Return</option>
            <option value="damage">Damage</option>
            <option value="lost">Lost</option>
            <option value="discard">Discard</option>
        </select>

        <!-- Items Dropdown -->
        <label for="item_id" class="form-label">Item:</label>
        <select id="item_id" class="form-select bg-gray-100 border border-gray-300">
            <option value="">Select Item</option>
        </select>

        <!-- Delete Button -->
        <button id="delete_button" class="form-button bg-red-500 text-white font-semibold hover:bg-red-600 disabled:bg-gray-400" disabled>Delete</button>
    </div>

    <script>
        $(document).ready(function () {
            // Populate items based on selected transaction type
            $('#transaction_type').change(function () {
                const transactionType = $(this).val();
                if (transactionType) {
                    $.ajax({
                        url: 'issued_delete.php',
                        method: 'GET',
                        data: { transaction_type: transactionType },
                        success: function (data) {
                            const transactions = JSON.parse(data);
                            $('#item_id').empty().append('<option value="">Select Item</option>');
                            transactions.forEach(transaction => {
                                $('#item_id').append(
                                    `<option value="${transaction.transaction_id}">
                                        ${transaction.item_name} (Qty: ${transaction.quantity})
                                    </option>`
                                );
                            });
                            $('#delete_button').prop('disabled', true);
                        }
                    });
                } else {
                    $('#item_id').empty().append('<option value="">Select Item</option>');
                    $('#delete_button').prop('disabled', true);
                }
            });

            // Enable delete button when an item is selected
            $('#item_id').change(function () {
                $('#delete_button').prop('disabled', !$(this).val());
            });

            // Handle delete action
            $('#delete_button').click(function () {
                const transactionId = $('#item_id').val();
                if (transactionId) {
                    $.ajax({
                        url: 'issued_delete.php',
                        method: 'POST',
                        data: { transaction_id: transactionId },
                        success: function (response) {
                            const res = JSON.parse(response);
                            if (res.success) {
                                swal("Success", res.message, "success");
                                setTimeout(() => location.reload(), 2000);
                            } else {
                                swal("Error", res.message, "error");
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
