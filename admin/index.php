<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

$counts = [];
$tables = ['Users', 'Books', 'Publishers', 'Categories', 'Orders', 'Payments', 'Wishlists', 'ShoppingCarts'];

foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    $counts[strtolower($table)] = $result->fetch_assoc()['count'];
}

// Get count of active books separately
$result = $conn->query("SELECT COUNT(*) as count FROM Books WHERE is_active = TRUE");
$counts['active_books'] = $result->fetch_assoc()['count'];

$recent_orders = $conn->query("
    SELECT o.order_id, o.order_date, o.total_amount, o.status, u.username 
    FROM Orders o
    JOIN Users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Get total revenue from completed payments
$revenue_result = $conn->query("
    SELECT SUM(amount) as revenue 
    FROM Payments 
    WHERE status = 'Completed'
");
$revenue = $revenue_result->fetch_assoc()['revenue'] ?? 0;
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Customers</h5>
                    <p class="card-text display-6"><?php echo $counts['users']; ?></p>
                    <a href="users/list.php" class="text-white">View all</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Active Books</h5>
                    <p class="card-text display-6"><?php echo $counts['active_books']; ?></p>
                    <a href="books/list.php" class="text-white">View all</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Orders</h5>
                    <p class="card-text display-6"><?php echo $counts['orders']; ?></p>
                    <a href="orders/list.php" class="text-white">View all</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Revenue</h5>
                    <p class="card-text display-6">
                        ₹<?php echo number_format($revenue, 2); ?>
                    </p>
                    <a href="payments/list.php" class="text-white">View payments</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><a href="orders/view.php?id=<?php echo $order['order_id']; ?>">#<?php echo $order['order_id']; ?></a></td>
                                    <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo $order['username']; ?></td>
                                    <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><span class="badge bg-<?php 
                                        switch($order['status']) {
                                            case 'Pending': echo 'warning'; break;
                                            case 'Processing': echo 'info'; break;
                                            case 'Shipped': echo 'primary'; break;
                                            case 'Delivered': echo 'success'; break;
                                            case 'Cancelled': echo 'danger'; break;
                                            default: echo 'secondary';
                                        }
                                    ?>"><?php echo $order['status']; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Stats</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Publishers
                            <span class="badge bg-primary rounded-pill"><?php echo $counts['publishers']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Categories
                            <span class="badge bg-primary rounded-pill"><?php echo $counts['categories']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Wishlist Items
                            <span class="badge bg-primary rounded-pill"><?php echo $counts['wishlists']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Active Carts
                            <span class="badge bg-primary rounded-pill"><?php echo $counts['shoppingcarts']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>