<?php
include '../config/db.php';
include '../includes/header.php';

// Initialize the filter conditions array
$conditions = ["is_active = 1"];

// Handle the search query
if (!empty($_GET['query'])) {
    $search = mysqli_real_escape_string($conn, $_GET['query']);
    $conditions[] = "(title LIKE '%$search%' OR author_name LIKE '%$search%')";
}

// Handle the category filter
if (!empty($_GET['category'])) {
    $category_id = (int) $_GET['category'];
    $conditions[] = "category_id = $category_id";
}

// Handle the price range filter
if (!empty($_GET['price_range'])) {
    list($min, $max) = explode('-', $_GET['price_range']);
    $min = (int)$min;
    $max = (int)$max;
    $conditions[] = "price BETWEEN $min AND $max";
}

// Combine all the conditions to form the WHERE clause
$whereClause = implode(" AND ", $conditions);

// Final query with dynamic filtering
$query = "SELECT * FROM books WHERE $whereClause ORDER BY title ASC";
$result = mysqli_query($conn, $query);

?>

<!-- Browse Page Filter Section -->
<section class="bookstore-browse-filter">
    <div class="bookstore-container">
        <div class="bookstore-filter-header">
            <h2>Browse All Books</h2>
            <p>Refine your search using filters</p>
        </div>

        <form class="bookstore-filter-bar" method="GET">
            <input type="text" name="query" placeholder="Search..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" />

            <select name="category">
                <option value="">Category</option>
                <?php
                $cat_query = "SELECT category_id, name FROM categories";
                $cat_result = mysqli_query($conn, $cat_query);
                while ($cat = mysqli_fetch_assoc($cat_result)) {
                    $selected = (isset($_GET['category']) && $_GET['category'] == $cat['category_id']) ? 'selected' : '';
                    echo '<option value="' . $cat['category_id'] . '" ' . $selected . '>' . htmlspecialchars($cat['name']) . '</option>';
                }
                ?>
            </select>

            <select name="price_range">
                <option value="">Price</option>
                <option value="0-199" <?php echo isset($_GET['price_range']) && $_GET['price_range'] == '0-199' ? 'selected' : ''; ?>>Under ₹200</option>
                <option value="200-499" <?php echo isset($_GET['price_range']) && $_GET['price_range'] == '200-499' ? 'selected' : ''; ?>>₹200–499</option>
                <option value="500-999" <?php echo isset($_GET['price_range']) && $_GET['price_range'] == '500-999' ? 'selected' : ''; ?>>₹500–999</option>
                <option value="1000-9999" <?php echo isset($_GET['price_range']) && $_GET['price_range'] == '1000-9999' ? 'selected' : ''; ?>>₹1000+</option>
            </select>

            <button type="submit" class="btn btn-primary">Apply</button>
        </form>
    </div>
</section>

<!-- Books Listing Section -->
<section class="bookstore-container">
    <div class="books-grid">
        <?php
        if (mysqli_num_rows($result) > 0):
            while ($book = mysqli_fetch_assoc($result)):
        ?>
        <div class="book-card">
            <div class="book-img">
                <img src="<?php echo !empty($book['cover_image_url']) ? htmlspecialchars($book['cover_image_url']) : 'assets/images/img.jpeg'; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
            </div>
            <div class="book-info">
                <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                <p class="book-author">By <?php echo htmlspecialchars($book['author_name']); ?></p>
                <p class="book-price">₹<?php echo $book['price']; ?></p>
                <div class="book-actions">
                    <a href="http://localhost/bookstore/user/cart/add.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-primary">Add to Cart</a>
                    <a href="wishlist/add.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-outline">Wishlist</a>
                </div>
            </div>
        </div>
        <?php
            endwhile;
        else:
            echo "<p class='text-center'>No books found.</p>";
        endif;
        ?>
    </div>
</section>


<?php include '../includes/footer.php'; ?>
