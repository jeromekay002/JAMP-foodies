<?php
include("include_front/navbar.php");
?>

<section class="food-management">
    <div class="food-container">
        <div class="header">
            <h2>Food Management</h2>
            <button class="add-btn" data-bs-toggle="modal" data-bs-target="#addFoodModal">
                <i class="fas fa-plus"></i> Add New Food
            </button>

        </div>

        <div class="search-bar">
            <input type="text" id="foodSearchInput" placeholder="Search food...">
            <button><i class="fas fa-search"></i></button>
        </div>


        <table class="food-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $get_all_foods_sql = "SELECT * FROM food";
                $get_all_foods_res = mysqli_query($connect, $get_all_foods_sql);
                if (!$get_all_foods_res) {
                    die("Get all foods sql error: " . mysqli_error($connect));
                }
                if (mysqli_num_rows($get_all_foods_res) > 0) {
                    while ($row = mysqli_fetch_assoc($get_all_foods_res)) {

                        $food_image = $row['food_image'];
                        $food_name = $row['food_name'];
                        $category = $row['product_category'];
                        $price = $row['price'];
                        $quantity = $row['quantity'];
                        $status = $row['status'];

                        // Check and update status based on quantity
                        if ($quantity <= 0) {
                            $status = "Out of Stock";
                        } elseif ($quantity <= 5) {
                            $status = "Low Stock";
                        } else {
                            $status = "In Stock";
                        }
                ?>
                        <tr>
                            <td>
                                <?php if (!empty($food_image)) { ?>
                                    <img src="../images/food/<?php echo htmlspecialchars($food_image); ?>" alt="Food Image">
                                <?php } else {
                                    echo "Food Image Not available";
                                } ?>
                            </td>
                            <td><?php echo htmlspecialchars($food_name); ?></td>
                            <td><?php echo htmlspecialchars($category); ?></td>
                            <td>Ksh <?php echo number_format($price, 2); ?></td>
                            <td><?php echo htmlspecialchars($quantity); ?></td>
                            <td>
                                <?php
                                if ($status == "In Stock") {
                                    echo "<span class='status in-stock'>$status</span>";
                                } elseif ($status == "Out of Stock") {
                                    echo "<span class='status out-stock'>$status</span>";
                                } elseif ($status == "Low Stock") {
                                    echo "<span class='status low-stock'>$status</span>";
                                }
                                ?>
                            </td>
                            <td style="display: flex;">
                                <button class="edit-btn edit-food-btn" title="Edit"
                                    data-id="<?php echo $row['food_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['food_name']); ?>"
                                    data-category="<?php echo htmlspecialchars($row['product_category']); ?>"
                                    data-price="<?php echo $row['price']; ?>"
                                    data-quantity="<?php echo $row['quantity']; ?>"
                                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                    data-status="<?php echo $row['status']; ?>"
                                    data-image="../images/food/<?php echo htmlspecialchars($row['food_image']); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>


                                <button class="delete-btn" data-id="<?php echo $row['food_id']; ?>" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>

                        </tr>
                <?php
                    }
                } else {
                    echo "<div class='text-center text-danger'>Foods Not Available</div>";
                }
                ?>


            </tbody>
        </table>
    </div>
</section>

<!-- Add Food Modal -->
<div class="modal fade" id="addFoodModal" tabindex="-1" aria-labelledby="addFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header" style="border-bottom: none; position: relative;">
                <h1 id="addFoodModalLabel">Add New Food</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="add/add_food.php" id="addFoodForm" enctype="multipart/form-data" method="post">

                    <div class="mb-3">
                        <i class="fas fa-hamburger"></i>
                        <input type="text" name="food_name" placeholder="Food Name" required>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-th-large"></i>
                        <select name="category" required>
                            <option value="">Select Category</option>
                            <?php
                            $category_sql = "SELECT category_name FROM category WHERE status = 'Active'";
                            $category_res = mysqli_query($connect, $category_sql);
                            if (!$category_res) {
                                die("Get categories sql error: " . mysqli_error($connect));
                            }
                            while ($category_row = mysqli_fetch_assoc($category_res)) {
                                $category_name = htmlspecialchars($category_row['category_name']);
                                echo "<option value='$category_name'>$category_name</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-dollar-sign"></i>
                        <input type="number" name="price" placeholder="Price" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-box"></i>
                        <input type="number" name="quantity" placeholder="Stock Quantity" required>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-align-left"></i>
                        <input type="text" name="description" placeholder="Description">
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-check-circle"></i>
                        <select name="status" required>
                            <option value="In Stock">In Stock</option>
                            <option value="Out of Stock">Out of Stock</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-image"></i>
                        <input type="file" name="food_image" accept="image/*" required>
                    </div>

                    <div class="button-field">
                        <button type="submit" class="button">
                            <i class="fas fa-save"></i> Save Food
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Edit Food Modal -->
<div class="modal fade" id="editFoodModal" tabindex="-1" aria-labelledby="editFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header" style="border-bottom: none; position: relative;">
                <h1 id="editFoodModalLabel">Edit Food</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="update/update_food.php" id="editFoodForm" enctype="multipart/form-data" method="post">

                    <!-- Hidden ID -->
                    <input type="hidden" name="food_id" id="edit_food_id">

                    <!-- Current Image Preview -->
                    <div class="mb-3" style="background-color: #fff; ">
                        <label style="font-weight: bold; display: flex; text-wrap: nowrap; margin-bottom: 5px;">
                            Current Image
                        </label>
                        <img id="edit_food_image_preview" src="" alt="Food Image"
                            style="max-width: 300px; max-height: 200px; border-radius: 5px; margin-bottom: 10px;">
                    </div>

                    <!-- Food Name -->
                    <div class="mb-3">
                        <label style="font-weight: bold; display: flex; text-wrap: nowrap; margin-bottom: 5px;">
                            <i class="fas fa-hamburger"></i> Food Name
                        </label>
                        <input type="text" name="food_name" id="edit_food_name" placeholder="Food Name" required>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label style="font-weight: bold;  display: flex; text-wrap: nowrap; margin-bottom: 5px;">
                            <i class="fas fa-th-large"></i> Category
                        </label>
                        <select name="category" id="edit_category" required>
                            <option value="">Select Category</option>
                            <?php
                            $category_sql = "SELECT category_name FROM category WHERE status = 'Active'";
                            $category_res = mysqli_query($connect, $category_sql);
                            if (!$category_res) {
                                die("Get categories sql error: " . mysqli_error($connect));
                            }
                            while ($category_row = mysqli_fetch_assoc($category_res)) {
                                $category_name = htmlspecialchars($category_row['category_name']);
                                echo "<option value='$category_name'>$category_name</option>";
                            }
                            ?>

                        </select>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label style="font-weight: bold;  display: flex; text-wrap: nowrap; margin-bottom: 5px;">
                            <i class="fas fa-dollar-sign"></i> Price
                        </label>
                        <input type="number" name="price" id="edit_price" placeholder="Price" step="0.01" required>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-3">
                        <label style="font-weight: bold;  display: flex; text-wrap: nowrap; margin-bottom: 5px;">
                            <i class="fas fa-box"></i> Stock Quantity
                        </label>
                        <input type="number" name="quantity" id="edit_quantity" placeholder="Stock Quantity" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label style="font-weight: bold;  display: flex; text-wrap: nowrap; margin-bottom: 5px;">
                            <i class="fas fa-align-left"></i> Description
                        </label>
                        <input type="text" name="description" id="edit_description" placeholder="Description">
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label style="font-weight: bold;  display: flex; text-wrap: nowrap; margin-bottom: 5px;">
                            <i class="fas fa-check-circle"></i> Status
                        </label>
                        <select name="status" id="edit_status" required>
                            <option value="In Stock">In Stock</option>
                            <option value="Out of Stock">Out of Stock</option>
                        </select>
                    </div>

                    <!-- New Image Upload -->
                    <small class="text-muted" style="font-size: 12px;">Leave empty to keep current image</small>
                    <div class="mb-3">
                        <label style="font-weight: bold; display: flex; text-wrap: nowrap; margin-bottom: 5px;">
                            <i class="fas fa-image"></i> Upload New Image
                        </label>
                        <input type="file" name="food_image" accept="image/*">

                    </div>

                    <!-- Submit Button -->
                    <div class="button-field">
                        <button type="submit" class="button">
                            <i class="fas fa-save"></i> Update Food
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.edit-food-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_food_id').value = this.dataset.id;
            document.getElementById('edit_food_name').value = this.dataset.name;
            document.getElementById('edit_category').value = this.dataset.category;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_quantity').value = this.dataset.quantity;
            document.getElementById('edit_description').value = this.dataset.description;
            document.getElementById('edit_status').value = this.dataset.status;

            const imagePath = this.dataset.image; // already includes ../images/food/
            const previewImg = document.getElementById('edit_food_image_preview');
            if (imagePath) {
                previewImg.src = imagePath;
            } else {
                previewImg.src = "../images/placeholder.png"; // optional fallback image
            }

            new bootstrap.Modal(document.getElementById('editFoodModal')).show();
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("foodSearchInput");
        const tableRows = document.querySelectorAll(".food-table tbody tr");

        searchInput.addEventListener("keyup", function() {
            const query = searchInput.value.toLowerCase();

            tableRows.forEach(row => {
                const foodName = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
                const category = row.querySelector("td:nth-child(3)").textContent.toLowerCase();

                if (foodName.includes(query) || category.includes(query)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });
</script>

<?php
include("include_front/footer.php");
?>