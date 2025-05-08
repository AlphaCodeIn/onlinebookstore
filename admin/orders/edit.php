<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid order ID.";
    header("Location: index.php");
    exit();
}

$order_id = (int) $_GET['id'];

// Fetch order
$sql = "SELECT o.*, u.first_name, u.last_name, u.email 
        FROM Orders o 
        JOIN Users u ON o.user_id = u.user_id 
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Order not found.";
    header("Location: index.php");
    exit();
}

$order = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    $total_amount = $_POST['total_amount'] ?? 0;

    $valid_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

    if (!in_array($status, $valid_statuses)) {
        $_SESSION['error'] = "Invalid status selected.";
    } elseif (!is_numeric($total_amount) || $total_amount <= 0) {
        $_SESSION['error'] = "Invalid total amount.";
    } else {
        $update_sql = "UPDATE Orders SET status = ?, total_amount = ? WHERE order_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sdi", $status, $total_amount, $order_id);
        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Order updated successfully.";
            header("Location: view.php?id=$order_id");
            exit();
        } else {
            $_SESSION['error'] = "Failed to update order.";
        }
    }
}
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Order #<?= $order['order_id'] ?></h1>
        <a href="view.php?id=<?= $order['order_id'] ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Order
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="edit.php?id=<?= $order_id ?>">
                <div class="mb-3">
                    <label class="form-label">Customer</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?>" disabled>
                </div>

                <div class="mb-3">
    <label class="form-label">Total Amount</label>
    <div class="form-control bg-light" readonly>
        ₹<?= number_format($order['total_amount'], 2) ?>
    </div>
    <input type="hidden" name="total_amount" value="<?= htmlspecialchars($order['total_amount']) ?>">
</div>


                <div class="mb-3">
                    <label for="status" class="form-label">Order Status</label>
                    <select class="form-select" name="status" required>
                        <?php
                        $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
                        foreach ($statuses as $s) {
                            $selected = ($order['status'] === $s) ? 'selected' : '';
                            echo "<option value=\"$s\" $selected>$s</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Order</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
