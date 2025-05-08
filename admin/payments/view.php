<?php
require_once '../config/db.php'; // Database connection
require_once '../includes/auth.php'; // Authentication check
require_once '../includes/header.php'; // Include header (consistent design)
require_once '../includes/sidebar.php'; // Include sidebar (consistent design)

// Check if the 'id' parameter is set and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $payment_id = (int) $_GET['id'];

    // Fetch payment details
    $sql = "SELECT p.payment_id, p.amount, p.payment_date, p.payment_method, p.transaction_id, p.status AS payment_status, 
                   o.order_id, o.total_amount, o.order_date, u.first_name, u.last_name, u.email
            FROM Payments p
            JOIN Orders o ON p.order_id = o.order_id
            JOIN Users u ON o.user_id = u.user_id
            WHERE p.payment_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $payment_result = $stmt->get_result();

    if ($payment_result->num_rows > 0) {
        $payment = $payment_result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Payment not found.";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid payment ID.";
    header("Location: index.php");
    exit();
}
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Payment Details - Payment #<?php echo $payment['payment_id']; ?></h1>
        <a href="list.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Payments
        </a>
    </div>

    <!-- Payment Information -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Payment Information</h5>
            <p><strong>Payment ID:</strong> <?php echo $payment['payment_id']; ?></p>
            <p><strong>Order ID:</strong> <a href="../orders/view.php?id=<?php echo $payment['order_id']; ?>">#<?php echo $payment['order_id']; ?></a></p>
            <p><strong>Payment Date:</strong> <?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></p>
            <p><strong>Amount Paid:</strong> ₹<?php echo number_format($payment['amount'], 2); ?></p>
            <p><strong>Payment Method:</strong> <?php echo $payment['payment_method']; ?></p>
            <p><strong>Transaction ID:</strong> <?php echo $payment['transaction_id'] ?: 'N/A'; ?></p>
            <p><strong>Payment Status:</strong> 
                <span class="badge bg-<?php echo ($payment['payment_status'] == 'Completed' ? 'success' : 'warning'); ?>">
                    <?php echo $payment['payment_status']; ?>
                </span>
            </p>
        </div>
    </div>

    <!-- Order Information -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Order Information</h5>
            <p><strong>Order Date:</strong> <?php echo date('M j, Y', strtotime($payment['order_date'])); ?></p>
            <p><strong>Order Total Amount:</strong> ₹<?php echo number_format($payment['total_amount'], 2); ?></p>
        </div>
    </div>

    <!-- User Information -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">User Information</h5>
            <p><strong>Name:</strong> <?php echo $payment['first_name'] . ' ' . $payment['last_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $payment['email']; ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
