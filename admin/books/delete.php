<?php
require_once '../includes/auth.php';  // To check for authentication
require_once '../config/db.php';      // For database connection

// Ensure a valid book ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect if no valid book ID is provided
    $_SESSION['error_message'] = 'Invalid book ID.';
    header('Location: list.php');
    exit;
}

$book_id = (int)$_GET['id'];  // Ensure ID is an integer

// Check if the book exists in the database
$query = "SELECT * FROM Books WHERE book_id = $book_id LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    // Redirect if the book does not exist
    $_SESSION['error_message'] = 'Book not found.';
    header('Location: list.php');
    exit;
}

// Proceed with deletion
$delete_query = "DELETE FROM Books WHERE book_id = $book_id";
if ($conn->query($delete_query)) {
    // Redirect with success message
    $_SESSION['success_message'] = 'Book deleted successfully.';
    header('Location: list.php');
    exit;
} else {
    // If deletion fails, show an error
    $_SESSION['error_message'] = 'Error deleting book. Please try again.';
    header('Location: list.php');
    exit;
}
?>
