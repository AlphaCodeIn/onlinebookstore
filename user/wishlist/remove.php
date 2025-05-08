 
<?php
include '../config/db.php';

// You can replace this with session-based user ID if implemented
$user_id = 1; // Static user ID for now (e.g., you can change this later)

// Get the book ID from the query parameter
$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;

// Ensure the book ID is valid
if ($book_id <= 0) {
    echo "Invalid book ID.";
    exit;
}

// Prepare SQL query to delete the book from the wishlist
$stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND book_id = ?");
$stmt->bind_param("ii", $user_id, $book_id);

// Execute the query
if ($stmt->execute()) {
    echo "<p>Book removed from your wishlist successfully. <a href='view.php'>View your wishlist</a>.</p>";
} else {
    echo "<p>There was an error removing the book from your wishlist. Please try again later.</p>";
}
?>
