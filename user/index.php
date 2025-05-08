<?php
include 'config/db.php';  
include 'includes/header.php'; 
include __DIR__ . '/includes/auth_check.php';
?>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <div class="hero-content">
      <h1>Discover Your Next Favorite Book</h1>
      <p>Explore our vast collection of books from Indian and international authors across all genres.</p>
      <div class="discover">
        <a href="http://localhost/bookstore/user/books/browse.php">DISCOVER MORE BOOKS</a>
      </div>
    </div>
  </div>
</section>

<section class="container">
  <div class="section-title">
    <h2>Featured Books</h2>
  </div>
  
  <div class="books-grid">
    <?php
   
    $query = "SELECT * FROM books WHERE is_featured = 1 AND is_active = 1 LIMIT 6";
    $result = mysqli_query($conn, $query);
    
    while($book = mysqli_fetch_assoc($result)):
    ?>
    <div class="book-card">
      <div class="book-img">
        <img src="<?php echo $book['cover_image_url']; ?>" alt="<?php echo $book['title']; ?>">
      </div>
      <div class="book-info">
        <h3 class="book-title"><?php echo $book['title']; ?></h3>
        <p class="book-author">By <?php echo $book['author_name']; ?></p>
        <p class="book-price">₹<?php echo $book['price']; ?></p>
        <div class="book-actions">
          <a href="cart/add.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-primary">Add to Cart</a>
          <a href="wishlist/add.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-outline">Wishlist</a>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
  
  <div class="text-center">
    <a href="http://localhost/bookstore/user/books/browse.php" class="btn btn-primary margin-btn">View All Books</a>
  </div>
</section>

<section class="categories">
  <div class="container">
    <div class="section-title">
      <h2>Browse Categories</h2>
    </div>
    
    <div class="category-list">
            <a href="books/browse.php" class="category-card">
        <i class="fas fa-book"></i>
        <h3>Fiction</h3>
      </a>
            <a href="books/browse.php" class="category-card">
        <i class="fas fa-book"></i>
        <h3>Non-Fiction</h3>
      </a>
            <a href="books/browse.php" class="category-card">
        <i class="fas fa-book"></i>
        <h3>Science</h3>
      </a>
            <a href="categories/view.php" class="category-card">
        <i class="fas fa-book"></i>
        <h3>Biography</h3>
      </a>
            <a href="categories/view.php" class="category-card">
        <i class="fas fa-book"></i>
        <h3>History</h3>
      </a>
          </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
