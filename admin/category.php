<?php
include("include_front/navbar.php");
?>

<section class="category-management">
    <div class="food-container">
        <div class="header" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <h2>Category Management</h2>
            <button class="add-btn" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Add New Category
            </button>
        </div>

        <div class="search-bar">
            <input type="text" id="categorySearchInput" placeholder="Search category...">
            <button><i class="fas fa-search"></i></button>
        </div>


        <div class="table-wrapper">
            <table class="food-table">
                <thead style="min-width: 100%;">
                    <tr>
                        <th>Category Image</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_all_categories_sql = "SELECT * FROM category";
                    $get_all_categories_res = mysqli_query($connect, $get_all_categories_sql);
                    if (!$get_all_categories_res) {
                        die("Error in sql: " . mysqli_error($connect));
                    }
                    if (mysqli_num_rows($get_all_categories_res) > 0) {
                        while ($row = mysqli_fetch_assoc($get_all_categories_res)) {
                    ?>
                            <tr>
                                <td data-label="Category Image">
                                    <?php
                                    $category_image = $row['category_image'];
                                    if ($category_image !== "") {
                                    ?>
                                        <img src="../images/categories/<?php echo $category_image; ?>" alt="Burgers"
                                            class="category-img">
                                    <?php
                                    } else {
                                        echo "Image not found";
                                    }
                                    ?>
                                </td>
                                <td data-label="Category Name"><?php echo $row['category_name']; ?></td>
                                <td data-label="Description"><?php echo $row['description']; ?></td>
                                <td>
                                    <?php
                                    $status = $row['status'];
                                    if ($status == "Active") {
                                        echo "<span class='status in-stock'>$status</span></td>";
                                    } else if ($status == "Inactive") {
                                        echo "<span class='status out-stock'>$status</span></td>";
                                    }
                                    ?>
                                <td>
                                <td data-label="Actions" style="display: flex;">
                                    <button class="edit-btn" title="Edit"
                                        data-id="<?php echo $row['category_id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['category_name']); ?>"
                                        data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                        data-status="<?php echo $row['status']; ?>"
                                        data-image="<?php echo $row['category_image']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="delete-btn" data-id="<?php echo $row['category_id']; ?>" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<div class='text-center text-danger'>Categories Not Available</div>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</section>


<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h1 id="addCategoryModalLabel">Add New Category</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="add/add_category.php" id="addCategoryForm" enctype="multipart/form-data" method="post">

                    <div class="mb-3">
                        <i class="fas fa-tag"></i>
                        <input type="text" name="category_name" placeholder="Category Name" required>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-align-left"></i>
                        <input type="text" name="description" placeholder="Description">
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-check-circle"></i>
                        <select name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-image"></i>
                        <input type="file" name="category_image" accept="image/*" class="file-input" required>
                    </div>

                    <div class="button-field">
                        <button type="submit" class="button">
                            <i class="fas fa-save"></i> Save Category
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h1 id="editCategoryModalLabel">Edit Category</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="update/update_category.php" id="editCategoryForm" enctype="multipart/form-data" method="post">

                    <!-- Hidden ID -->
                    <input type="hidden" name="category_id" id="edit_category_id">

                    <!-- Current Image Preview -->
                    <div class="mb-3" style="background-color: #fff;">
                        <label style="font-weight: bold; color: #333; display: flex; text-wrap: nowrap;">
                            Current Image:
                        </label>
                        <div>
                            <img id="edit_category_image_preview" src="" alt="Category Image"
                                style="max-width: 300px; border-radius: 5px; margin-bottom: 10px;">
                        </div>
                    </div>

                    <!-- Category Name -->
                    <div class="mb-3">
                        <label for="edit_category_name"
                            style="font-weight: bold; color: #333; margin-bottom: 5px; display: flex; text-wrap: nowrap;">
                            <i class="fas fa-tag" style="color: #ff9800; margin-right: 5px;"></i>
                            Category Name
                        </label>
                        <input type="text" name="category_name" id="edit_category_name"
                            placeholder="Enter category name" required
                            style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="edit_description"
                            style="font-weight: bold; color: #333; margin-bottom: 5px; display: flex; text-wrap: nowrap;">
                            <i class="fas fa-align-left" style="color: #3f51b5; margin-right: 5px;"></i>
                            Description
                        </label>
                        <input type="text" name="description" id="edit_description"
                            placeholder="Enter category description"
                            style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="edit_status"
                            style="font-weight: bold; color: #333; margin-bottom: 5px; display: flex; text-wrap: nowrap;">
                            <i class="fas fa-check-circle" style="color: #4caf50; margin-right: 5px;"></i>
                            Status
                        </label>
                        <select name="status" id="edit_status" required
                            style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>



                    <!-- New Image Upload -->
                    <small style="color: #777; font-size: 12px;">Leave empty to keep the current image</small>
                    <div class="mb-3">
                        <label for="edit_category_image"
                            style="font-weight: bold; color: #333; margin-bottom: 5px; display: flex; text-wrap: nowrap;">
                            <i class="fas fa-image" style="color: #9c27b0; margin-right: 5px;"></i>
                            Upload New Image
                        </label>
                        <input type="file" name="category_image" id="edit_category_image" accept="image/*"
                            style="width: 100%; padding: 6px; border-radius: 5px; border: 1px solid #ccc;">

                    </div>

                    <!-- Submit Button -->
                    <div class="button-field" style="margin-top: 15px;">
                        <button type="submit"
                            style="background-color: #2196f3; color: white; padding: 10px 15px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
                            <i class="fas fa-save" style="margin-right: 5px;"></i> Update Category
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".edit-btn").forEach(function(btn) {
            btn.addEventListener("click", function() {
                // Get data attributes
                const id = this.dataset.id;
                const name = this.dataset.name;
                const description = this.dataset.description;
                const status = this.dataset.status;
                const image = this.dataset.image;

                // Fill the modal fields
                document.getElementById("edit_category_id").value = id;
                document.getElementById("edit_category_name").value = name;
                document.getElementById("edit_description").value = description;
                document.getElementById("edit_status").value = status;
                document.getElementById("edit_category_image_preview").src =
                    "../images/categories/" + image;

                // Show the modal
                new bootstrap.Modal(document.getElementById("editCategoryModal")).show();
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("categorySearchInput");
        const tableRows = document.querySelectorAll(".food-table tbody tr");

        searchInput.addEventListener("keyup", function() {
            const query = searchInput.value.toLowerCase();

            tableRows.forEach(row => {
                const name = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
                const desc = row.querySelector("td:nth-child(3)").textContent.toLowerCase();

                if (name.includes(query) || desc.includes(query)) {
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