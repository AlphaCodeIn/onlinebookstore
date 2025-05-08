<?php
session_start();
include '../config/db.php';  // Database connection

// Check if the cart is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='http://localhost/bookstore/user/'>Browse Books</a></p>";
    exit;
}

$cart_items = $_SESSION['cart']; 
$total_price = 0;

?>

<!-- Cart Page UI -->
<section class="cart-page">
    <div class="container">
        <h2>Your Cart</h2>
        
        <div class="cart-table-container">
            <form method="POST" action="cart.php">  <!-- Form to submit updated quantities -->
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through cart items and display them
                        foreach ($cart_items as $book_id => $item) {
                            $total_item_price = $item['price'] * $item['quantity'];
                            $total_price += $total_item_price;
                        ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($item['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="cart-book-image"></td>
                                <td><?php echo htmlspecialchars($item['author_name']); ?></td>
                                <td>₹<?php echo $item['price']; ?></td>
                                <td>
                                    <!-- Disable quantity field by setting 'disabled' attribute -->
                                    <input type="number" name="quantity[<?php echo $book_id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="5" class="cart-quantity" disabled />
                                </td>
                                <td>₹<?php echo $total_item_price; ?></td>
                                <td>
                                    <a href="remove.php?book_id=<?php echo $book_id; ?>" class="remove-btn">Remove</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="cart-summary">
                    <div class="summary-item">
                        <strong>Total:</strong> ₹<?php echo $total_price; ?>
                    </div>
                    <div class="cart-actions">
                        <a href="http://localhost/bookstore/user/books/browse.php" class="btn btn-secondary">Continue Shopping</a>
                        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
