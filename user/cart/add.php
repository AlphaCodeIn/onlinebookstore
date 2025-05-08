<?php
session_start();
include '../config/db.php';

if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    header("Location: ../../index.php");
    exit;
}

$book_id = (int) $_GET['book_id'];

$query = "SELECT * FROM books WHERE book_id = $book_id AND is_active = 1 LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Book not found.";
    header("Location: ../../index.php");
    exit;
}

$book = mysqli_fetch_assoc($result);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$book_id])) {
    $_SESSION['cart'][$book_id]['quantity'] += 1;
} else {
    $_SESSION['cart'][$book_id] = [
        'book_id' => $book['book_id'],
        'title' => $book['title'],
        'author_name' => $book['author_name'],
        'price' => $book['price'],
        'quantity' => 1,
        'cover_image_url' => $book['cover_image_url']
    ];
}

$_SESSION['success'] = $book['title'] . " has been added to your cart.";
header("Location: view.php");
exit;
?>
