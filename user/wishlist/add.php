<?php
include '../config/db.php';

$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;

// Ensure the book_id is valid
if ($book_id <= 0) {
    echo "Invalid book ID.";
    exit;
}

// You can either set a static user ID or remove the user ID entirely
// For example, you can set a static user_id for testing purposes:
// $user_id = 1; // Example static user ID

// OR: If you don't want to associate it with any user, remove the user_id
$user_id = 1; // Static value or you can hardcode it for now

// Check if the book is already in the wishlist
$stmt = $conn->prepare("SELECT * FROM wishlists WHERE user_id = ? AND book_id = ?");
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Book already in wishlist
    echo "<p>This book is already in your wishlist. <a href='view.php'>View your wishlist</a>.</p>";
} else {
    // Add the book to the wishlist
    $stmt = $conn->prepare("INSERT INTO wishlists (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $book_id);

    if ($stmt->execute()) {
        echo "<p>Book added to your wishlist successfully. <a href='view.php'>View your wishlist</a>.</p>";
    } else {
        echo "<p>There was an error adding the book to your wishlist. Please try again later.</p>";
    }
}
?>
