<?php
session_start();
include '../config/db.php';

if (!isset($_GET['order_id'])) {
    echo "Order ID not provided.";
    exit;
}

$order_id = (int) $_GET['order_id'];

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="order-success-container">
        <h2>Order Successfully Placed!</h2>
        <p>Order ID: <?php echo $order_id; ?></p>
        <p>Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?></p>
        <p>Status: <?php echo $order['status']; ?></p>
        <p>Payment Method: <?php echo $order['payment_method']; ?></p>
        <a href="http://localhost/bookstore/user/">Go to Home</a>
    </div>
</body>
</html>
