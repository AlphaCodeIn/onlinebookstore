<?php
session_start();
include '../config/db.php';

// Fetch wishlist items
// Remove the login check for now
$user_id = 1; // Placeholder for user_id; this could be hardcoded or from a session if necessary.

$stmt = $conn->prepare("SELECT b.book_id, b.title, b.author_name, b.price, b.cover_image_url 
                        FROM wishlists w
                        JOIN books b ON w.book_id = b.book_id
                        WHERE w.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$wishlist_items = [];
while ($row = $result->fetch_assoc()) {
    $wishlist_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Wishlist - Bookstore</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>

<div class="wishlist-container">
    <div class="wishlist-header">
        <h2>Your Wishlist</h2>
        <p>Books you love and want to save for later</p>
    </div>

    <?php if (empty($wishlist_items)): ?>
        <p>Your wishlist is empty. <a href="../../index.php">Browse Books</a></p>
    <?php else: ?>
        <div class="wishlist-items">
            <?php foreach ($wishlist_items as $item): ?>
                <div class="wishlist-item">
                    <img src="<?php echo htmlspecialchars($item['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <div class="details">
                        <div><strong><?php echo htmlspecialchars($item['title']); ?></strong></div>
                        <div>by <?php echo htmlspecialchars($item['author_name']); ?></div>
                        <div>₹<?php echo $item['price']; ?></div>
                    </div>
                    <div>
                        <a href="remove.php?book_id=<?php echo $item['book_id']; ?>" class="remove-btn">Remove</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="wishlist-summary">
        <a href="http://localhost/bookstore/user/books/browse.php" class="btn">ContinueBuY Book</a>
    </div>
</div>

</body>
</html>

<?php include '../includes/footer.php'; ?>
