<?php include("include_front/menu.php"); ?>

<!-- food search  -->
<section class="food-search">
    <div class="container">
        <form action="food_search.php" method="POST">
            <input type="search" class="form-control" name="search" placeholder="Search for Food.." required>
            <input type="submit" name="submit" value="Search" class="btn">
        </form>
    </div>
</section>
<!-- food categories -->
<section class="categories">
    <h2 class="head text-center">Explore Foods</h2>
    <div class="container">
        <?php
        $get_category_sql = "SELECT * FROM category WHERE status='In Stock' LIMIT 3";
        $get_category_result = mysqli_query($connect, $get_category_sql);
        if (!$get_category_result) {
            die("Get categort error: " . mysqli_error($connect));
        }
        if (mysqli_num_rows($get_category_result) > 0) {
            while ($category_row = mysqli_fetch_assoc($get_category_result)) {
                $category_id = $category_row['category_id'];
                $category_name = $category_row['category_name'];
                $category_image = $category_row['category_image'];
        ?>
                <a href="category_foods.php?category_id=<?php echo $category_id; ?>">
                    <div class="box-3 float-container">
                        <?php
                        if ($category_image !== "") {
                        ?>
                            <img src="<?php echo $category_image; ?>" class="img-responsive rounded mx-auto d-block" alt="Food Category">
                        <?php
                            // echo '<img src="$category_image" alt="Category Image" class="img-responsive rounded mx-auto d-block">';
                        } else {
                            echo "Category Image Not Found";
                        }
                        ?>
                        <h3 class="float-text"><?php echo $category_name; ?></h3>
                    </div>
                </a>
        <?php
            }
        } else {
            echo "<div class='text-center text-danger'>Categories Not Available</div>";
        }
        ?>
    </div>
</section>


<?php include("include_front/footer.php"); ?>