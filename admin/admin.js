document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const foodId = this.getAttribute('data-id');

            if (confirm("Are you sure you want to delete this food item?")) {
                // Redirect to the PHP delete handler with the food ID
                window.location.href = 'delete/delete_food.php?food_id=' + encodeURIComponent(foodId);
            }
        });
    });
});

document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function () {
        const categoryId = this.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this category?')) {
            // Create a form dynamically and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete/delete_category.php'; // Adjust path if needed

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'category_id';
            input.value = categoryId;
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }
    });
});

