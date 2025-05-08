<?php
session_start();
include '../config/db.php';

// Check if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: view.php");
    exit;
}

// Assign cart from session
$cart = $_SESSION['cart'];

// Fetch book details
$book_ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($book_ids), '?'));

$stmt = $conn->prepare("SELECT * FROM books WHERE book_id IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($book_ids)), ...$book_ids);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $id = $row['book_id'];
    $qty = isset($cart[$id]) ? (int) $cart[$id] : 1;
    $subtotal = $row['price'] * $qty;
    $total += $subtotal;
    $items[] = [
        'title' => $row['title'],
        'quantity' => $qty,
        'subtotal' => $subtotal
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Bookstore</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>

<div class="checkout-container">
    <div class="checkout-header">
        <h2>Checkout</h2>
        <p>Review your cart and confirm your order</p>
    </div>

    <?php foreach ($items as $book): ?>
        <div class="checkout-item">
            <div>
                <div class="title"><?php echo htmlspecialchars($book['title']); ?></div>
                <div>Qty: <?php echo $book['quantity']; ?></div>
            </div>
            <div>₹<?php echo number_format($book['subtotal'], 2); ?></div>
        </div>
    <?php endforeach; ?>

    <div class="checkout-summary">
        <form method="post" action="place_order.php">
            <div class="address-section">
                <h3>Shipping Address</h3>
                <textarea name="shipping_address" rows="4" required placeholder="Enter your shipping address (house no, street, city, state, pincode)"></textarea>
            </div>
            
            <div class="address-section">
                <h3>Billing Address</h3>
                <textarea name="billing_address" rows="4" required placeholder="Enter your billing address (house no, street, city, state, pincode)"></textarea>
                <label>
                    <input type="checkbox" id="same_as_shipping"> Same as shipping address
                </label>
            </div>
            
            <p class="total">Total: ₹<?php echo number_format($total, 2); ?></p>
            <button type="submit" class="btn">Place Order</button>
        </form>
    </div>
</div>

<script>
    // Copy shipping address to billing address when checkbox is checked
    document.getElementById('same_as_shipping').addEventListener('change', function() {
        if (this.checked) {
            document.querySelector('textarea[name="billing_address"]').value = 
                document.querySelector('textarea[name="shipping_address"]').value;
        }
    });
</script>

</body>
</html>