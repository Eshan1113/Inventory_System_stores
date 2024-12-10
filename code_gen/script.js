document.getElementById('categorySelect').addEventListener('change', function() {
    const categoryId = this.value;
    
    if (categoryId) {
        fetch(`get_subcategories.php?category_id=${categoryId}`)
            .then(response => response.json())
            .then(data => {
                const subcategorySelect = document.getElementById('subcategory');
                subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>'; // Reset subcategories
                
                data.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = `${subcategory.name} (${subcategory.code})`;
                    subcategorySelect.appendChild(option);
                });
            });
    }
});
