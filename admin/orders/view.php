<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/header.php'; // Consistent header
require_once '../includes/sidebar.php'; // Consistent sidebar

// Check if the 'id' parameter is set and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $order_id = (int) $_GET['id'];

    // Fetch order details (removed tracking_number from SELECT)
    $sql = "SELECT o.order_id, o.order_date, o.status, o.total_amount, o.shipping_address, o.billing_address, o.payment_method, u.first_name, u.last_name, u.email 
            FROM Orders o
            JOIN Users u ON o.user_id = u.user_id
            WHERE o.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_result = $stmt->get_result();

    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Order not found.";
        header("Location: index.php");
        exit();
    }

    $order_items_sql = "SELECT oi.quantity, oi.unit_price, b.title, b.author_name 
                        FROM OrderItems oi
                        JOIN Books b ON oi.book_id = b.book_id
                        WHERE oi.order_id = ?";
    $order_items_stmt = $conn->prepare($order_items_sql);
    $order_items_stmt->bind_param("i", $order_id);
    $order_items_stmt->execute();
    $order_items = $order_items_stmt->get_result();
} else {
    $_SESSION['error'] = "Invalid order ID.";
    header("Location: index.php");
    exit();
}
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order Details - Order #<?php echo $order['order_id']; ?></h1>
        <a href="list.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <!-- Order Information -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Order Information</h5>
            <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
            <p><strong>Order Date:</strong> <?php echo date('M j, Y', strtotime($order['order_date'])); ?></p>
            <p><strong>Status:</strong> <span class="badge bg-<?php echo ($order['status'] == 'Completed' ? 'success' : 'warning'); ?>"><?php echo $order['status']; ?></span></p>
            <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Shipping Address:</strong> <?php echo nl2br($order['shipping_address']); ?></p>
            <p><strong>Billing Address:</strong> <?php echo nl2br($order['billing_address']); ?></p>
            <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
            <!-- Removed Tracking Number -->
        </div>
    </div>

    <!-- User Information -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">User Information</h5>
            <p><strong>Name:</strong> <?php echo $order['first_name'] . ' ' . $order['last_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
        </div>
    </div>

    <!-- Order Items (Books in the order) -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Ordered Books</h5>
            <?php if ($order_items->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $order_items->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $item['title']; ?></td>
                                <td><?php echo $item['author_name']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>₹<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td>₹<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No items found in this order.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
