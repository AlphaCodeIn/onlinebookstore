<?php 
include '../includes/header.php';
include '../config/db.php';

$book_id = $_GET['id'] ?? 0;
$query = "SELECT b.*, c.name as category_name, p.name as publisher_name 
          FROM books b 
          JOIN categories c ON b.category_id = c.category_id 
          JOIN publishers p ON b.publisher_id = p.publisher_id 
          WHERE b.book_id = $book_id";
$result = mysqli_query($conn, $query);
$book = mysqli_fetch_assoc($result);

if(!$book) {
  header("Location: search.php");
  exit;
}
?>

<section class="container book-details">
  <div class="book-detail-container">
    <div class="book-image">
      <img src="<?php echo $book['cover_image_url']; ?>" alt="<?php echo $book['title']; ?>">
    </div>
    
    <div class="book-info">
      <h1><?php echo $book['title']; ?></h1>
      <p class="author">By <?php echo $book['author_name']; ?></p>
      
      <div class="rating">
        <div class="stars">
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
          <i class="fas fa-star-half-alt"></i>
        </div>
        <span>4.5 (120 reviews)</span>
      </div>
      
      <div class="price-section">
        <p class="price">₹<?php echo $book['price']; ?></p>
        <p class="availability"><?php echo $book['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?></p>
      </div>
      
      <div class="actions">
        <a href="../cart/add.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-primary">
          <i class="fas fa-shopping-cart"></i> Add to Cart
        </a>
        <a href="../wishlist/add.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-outline">
          <i class="fas fa-heart"></i> Add to Wishlist
        </a>
      </div>
      
      <div class="delivery-info">
        <p><i class="fas fa-truck"></i> Free delivery on orders over ₹500</p>
        <p><i class="fas fa-undo"></i> 7-day easy returns</p>
      </div>
    </div>
  </div>
  
  <div class="book-description">
    <h2>Description</h2>
    <p><?php echo $book['description']; ?></p>
    
    <div class="details-grid">
      <div class="detail-item">
        <h3>ISBN</h3>
        <p><?php echo $book['isbn']; ?></p>
      </div>
      <div class="detail-item">
        <h3>Publisher</h3>
        <p><?php echo $book['publisher_name']; ?></p>
      </div>
      <div class="detail-item">
        <h3>Publication Date</h3>
        <p><?php echo date('F j, Y', strtotime($book['publication_date'])); ?></p>
      </div>
      <div class="detail-item">
        <h3>Pages</h3>
        <p><?php echo $book['pages']; ?></p>
      </div>
      <div class="detail-item">
        <h3>Language</h3>
        <p><?php echo $book['language']; ?></p>
      </div>
      <div class="detail-item">
        <h3>Category</h3>
        <p><?php echo $book['category_name']; ?></p>
      </div>
    </div>
  </div>
  
  <div class="related-books">
    <h2>You May Also Like</h2>
    <div class="books-grid">
      <?php
      $related_query = "SELECT * FROM books 
                       WHERE category_id = {$book['category_id']} 
                       AND book_id != {$book['book_id']} 
                       LIMIT 4";
      $related_result = mysqli_query($conn, $related_query);
      
      while($related_book = mysqli_fetch_assoc($related_result)):
      ?>
      <div class="book-card">
        <div class="book-img">
          <img src="<?php echo $related_book['cover_image_url']; ?>" alt="<?php echo $related_book['title']; ?>">
        </div>
        <div class="book-info">
          <h3 class="book-title"><?php echo $related_book['title']; ?></h3>
          <p class="book-author">By <?php echo $related_book['author_name']; ?></p>
          <p class="book-price">₹<?php echo $related_book['price']; ?></p>
          <div class="book-actions">
            <a href="http://localhost/bookstore/user/cart/add.php?book_id=<?php echo $related_book['book_id']; ?>" class="btn btn-primary">Add to Cart</a>
            <a href="details.php?id=<?php echo $related_book['book_id']; ?>" class="btn btn-outline">Details</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>