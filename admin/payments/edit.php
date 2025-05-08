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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated data from the form
    $amount = $_POST['amount'];
    $payment_status = $_POST['payment_status'];
    $order_id = $payment['order_id']; // already fetched earlier

    // Validate input
    if (!is_numeric($amount) || $amount <= 0) {
        $_SESSION['error'] = "Please enter a valid amount.";
    } else {
        // Update the payment record
        $update_sql = "UPDATE Payments SET amount = ?, status = ? WHERE payment_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("dsi", $amount, $payment_status, $payment_id);

        if ($update_stmt->execute()) {

            // ✅ If payment is marked as Failed, cancel the order
            if ($payment_status === 'Failed') {
                $cancel_sql = "UPDATE Orders SET status = 'Cancelled' WHERE order_id = ?";
                $cancel_stmt = $conn->prepare($cancel_sql);
                $cancel_stmt->bind_param("i", $order_id);
                $cancel_stmt->execute();
            }

            $_SESSION['message'] = "Payment details updated successfully.";
            header("Location: view.php?id=$payment_id");
            exit();
        } else {
            $_SESSION['error'] = "Failed to update payment details. Please try again.";
        }
    }
}

?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Payment Details - Payment #<?php echo $payment['payment_id']; ?></h1>
        <a href="view.php?id=<?php echo $payment['payment_id']; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Payment
        </a>
    </div>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']; ?>
        </div>
    <?php unset($_SESSION['message']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; ?>
        </div>
    <?php unset($_SESSION['error']); endif; ?>

    <!-- Payment Edit Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="edit.php?id=<?php echo $payment['payment_id']; ?>">
            <div class="mb-3">
    <label class="form-label">Amount</label>
    <div class="form-control bg-light" readonly>
        ₹<?= number_format($payment['amount'], 2) ?>
    </div>
    <input type="hidden" name="amount" value="<?= htmlspecialchars($payment['amount']) ?>">
</div>


                <div class="mb-3">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select class="form-select" id="payment_status" name="payment_status" required>
                        <option value="Completed" <?php echo ($payment['payment_status'] == 'Completed' ? 'selected' : ''); ?>>Completed</option>
                        <option value="Pending" <?php echo ($payment['payment_status'] == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="Failed" <?php echo ($payment['payment_status'] == 'Failed' ? 'selected' : ''); ?>>Failed</option>
                        <option value="Refunded" <?php echo ($payment['payment_status'] == 'Refunded' ? 'selected' : ''); ?>>Refunded</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Payment</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
