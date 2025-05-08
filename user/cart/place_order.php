<?php
session_start();
include '../config/db.php';

// Check if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: view.php");
    exit;
}

$cart = $_SESSION['cart'];
$book_ids = array_keys($cart);

// Get addresses from POST
$shipping_address = trim($_POST['shipping_address']);
$billing_address = trim($_POST['billing_address']);

// Fetch book details
$placeholders = implode(',', array_fill(0, count($book_ids), '?'));
$stmt = $conn->prepare("SELECT book_id, price, title FROM books WHERE book_id IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($book_ids)), ...$book_ids);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];

while ($book = $result->fetch_assoc()) {
    $book_id = $book['book_id'];
    $qty = (int) $cart[$book_id];  // Ensure the quantity is treated as an integer
    $subtotal = $book['price'] * $qty;

    $items[] = [
        'book_id' => $book_id,
        'quantity' => $qty,
        'price' => $book['price']
    ];

    $total += $subtotal;
}

// Insert into orders table
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status, shipping_address, billing_address) VALUES (?, ?, ?, ?, ?, ?)");

// Static user ID for now
$user_id = 1;
$payment_method = 'COD';
$status = 'Pending';

if ($stmt === false) {
    die('Error preparing statement for orders table: ' . $conn->error);
}

$stmt->bind_param("idssss", $user_id, $total, $payment_method, $status, $shipping_address, $billing_address);
if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
} else {
    die('Error executing statement for orders table: ' . $stmt->error);
}


$item_stmt = $conn->prepare("INSERT INTO orderitems (order_id, book_id, quantity, unit_price) VALUES (?, ?, ?, ?)");

if ($item_stmt === false) {
    die('Error preparing statement for orderitems table: ' . $conn->error);
}

foreach ($items as $item) {
    $item_stmt->bind_param("iiid", $order_id, $item['book_id'], $item['quantity'], $item['price']);
    if (!$item_stmt->execute()) {
        die('Error executing statement for orderitems table: ' . $item_stmt->error);
    }
}

// Clear cart
unset($_SESSION['cart']);

// Redirect to success
header("Location: order_success.php?order_id=$order_id");
exit;
?>
