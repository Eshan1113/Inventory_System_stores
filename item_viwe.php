<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-50">
    <?php include('header1.php'); ?>

    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center">Item List</h1>

        <!-- Date Filter Section -->
        <div class="flex justify-between items-center mt-4 mb-6">
            <input type="text" id="searchBox" placeholder="Search by Item Name" class="border rounded p-2">

            <div class="flex">
                <input type="date" id="startDate" class="border rounded p-2 mx-2" />
                <input type="date" id="endDate" class="border rounded p-2" />
            </div>
        </div>

        <!-- Table to display items -->
        <table id="itemsTable" class="table-auto w-full bg-white shadow-md rounded">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Item Code</th>
                    <th class="border px-4 py-2">Local Item Code</th>
                    <th class="border px-4 py-2">Item Name</th>
                    <th class="border px-4 py-2">Specifications</th>
                    <th class="border px-4 py-2">Image</th>
                    <th class="border px-4 py-2">Location</th>
                    <th class="border px-4 py-2">Category</th>
                    <th class="border px-4 py-2">Quantity</th>
                    <th class="border px-4 py-2">Purchase Date</th>
                    <th class="border px-4 py-2">Purchase Price</th>
                    <th class="border px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody id="itemsTableBody">
                <!-- Data will be loaded here via AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Image Preview Modal -->
    <div id="imagePreviewModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 hidden">
        <div class="relative">
            <img id="previewImage" src="" alt="Image Preview" class="max-w-full max-h-screen rounded">
            <button id="closeModal" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2">&times;</button>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Fetch initial item data on page load
        fetchItems();

        // Fetch filtered data when search text or date range changes
        $('#searchBox, #startDate, #endDate').on('input change', function() {
            fetchItems();
        });

        // Fetch items with filters applied
        function fetchItems() {
            var searchTerm = $('#searchBox').val();
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            $.ajax({
                url: 'fetch_items.php',
                type: 'GET',
                data: {
                    search: searchTerm,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    $('#itemsTableBody').html(response);

                    // Add click event for images
                    $('.image-preview').on('click', function() {
                        var imageUrl = $(this).data('src');
                        $('#previewImage').attr('src', imageUrl);
                        $('#imagePreviewModal').removeClass('hidden');
                    });
                }
            });
        }

        // Close modal
        $('#closeModal').on('click', function() {
            $('#imagePreviewModal').addClass('hidden');
        });
    });
    </script>
</body>
</html>
