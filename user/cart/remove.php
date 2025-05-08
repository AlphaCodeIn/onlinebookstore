<?php
session_start();

if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    header("Location: view.php");
    exit;
}

$book_id = (int) $_GET['book_id'];

if (isset($_SESSION['cart'][$book_id])) {
    unset($_SESSION['cart'][$book_id]);
    $_SESSION['success'] = "Book removed from cart.";
} else {
    $_SESSION['error'] = "Book not found in cart.";
}

header("Location: view.php");
exit;
