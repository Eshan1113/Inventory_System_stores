<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <script src="css/jquery-3.6.0.min.js"></script>
    <link href="css/tailwind.min.css" rel="stylesheet">
    <title>Item List</title>
</head>
<body class="bg-gray-50">
    <?php include('header1.php'); ?>

    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center">Item List</h1>

        <!-- Filters Section -->
        <div class="flex justify-between items-center mt-4 mb-6">
            <input type="text" id="searchBox" placeholder="Search by Item Name" class="border rounded p-2 w-1/3">

            <div class="flex items-center">
                <input type="date" id="startDate" class="border rounded p-2 mx-2" />
                <input type="date" id="endDate" class="border rounded p-2" />
            </div>

            <select id="locationFilter" class="border rounded p-2 mx-2">
                <option value="">Select Location</option>
                <option value="Main Warehouse Store">Main Warehouse Store</option>
                <option value="KTI">KTI</option>
                <option value="Training School">Training School</option>
                <!-- Add all locations here -->
            </select>

            <select id="categoryFilter" class="border rounded p-2">
                <option value="">Select Category</option>
                <option value="Tools">Tools</option>
                <option value="Safety Equipment">Safety Equipment</option>
                <option value="Consumables">Consumables</option>
                <!-- Add all categories here -->
            </select>
        </div>

        <!-- Export Buttons -->
        <div class="flex justify-end mb-4">
            <button id="exportPdf" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Export as PDF</button>
            <button id="exportExcel" class="bg-green-500 text-white px-4 py-2 rounded">Export as Excel</button>
        </div>

        <!-- Table to display items -->
        <table id="itemsTable" class="table-auto w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-200">
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

            // Fetch filtered data when filters change
            $('#searchBox, #startDate, #endDate, #locationFilter, #categoryFilter').on('input change', function() {
                fetchItems();
            });

            // Fetch items with filters applied
            function fetchItems() {
                var searchTerm = $('#searchBox').val();
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var location = $('#locationFilter').val();
                var category = $('#categoryFilter').val();

                $.ajax({
                    url: 'fetch_items.php',
                    type: 'GET',
                    data: {
                        search: searchTerm,
                        start_date: startDate,
                        end_date: endDate,
                        location: location,
                        category: category
                    },
                    success: function(response) {
                        $('#itemsTableBody').html(response);

                        // Add click event for images
                        $('.image-preview').on('click', function() {
                            var imageUrl = $(this).data('src');
                            $('#previewImage').attr('src', imageUrl);
                            $('#imagePreviewModal').removeClass('hidden');
                        });
                    },
                    error: function() {
                        alert('Failed to fetch items. Please try again.');
                    }
                });
            }

            // Close the image preview modal
            $('#closeModal').on('click', function() {
                $('#imagePreviewModal').addClass('hidden');
            });

            // Export to PDF
            $('#exportPdf').on('click', function() {
                var params = getFilters();
                window.location.href = 'export_pdf.php?' + params;
            });

            // Export to Excel
            $('#exportExcel').on('click', function() {
                var params = getFilters();
                window.location.href = 'export_excel.php?' + params;
            });

            // Helper function to get filter parameters as query string
            function getFilters() {
                var searchTerm = $('#searchBox').val();
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var location = $('#locationFilter').val();
                var category = $('#categoryFilter').val();

                return $.param({
                    search: searchTerm,
                    start_date: startDate,
                    end_date: endDate,
                    location: location,
                    category: category
                });
            }
        });
    </script>
</body>
</html>
