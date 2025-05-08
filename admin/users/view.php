<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/list_functions.php';

// Configuration for users
$entity = 'users';
$entity_title = 'User';
$edit_url = 'edit.php';
$delete_url = 'delete.php';
$id_field = 'user_id'; // Primary key field name

// Fetching the user's data from the database
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $query = "SELECT * FROM Users WHERE user_id = $user_id LIMIT 1";
    $user = $conn->query($query)->fetch_assoc();

    if (!$user) {
        // Redirect to list page if user not found
        header('Location: index.php');
        exit;
    }
} else {
    // Redirect to list page if ID is not passed
    header('Location: index.php');
    exit;
}

// Include header and sidebar
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $entity_title; ?> Details</h1>
        <div class="btn-group">
            <a href="<?php echo $edit_url . '?id=' . $user[$id_field]; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?php echo $delete_url . '?id=' . $user[$id_field]; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">
                <i class="fas fa-trash"></i> Delete
            </a>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">User Information</h5>
            <ul class="list-unstyled">
                <li><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></li>
                <li><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
                <li><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></li>
                <li><strong>Address:</strong> <?php echo htmlspecialchars($user['address_line1']); ?>, <?php echo htmlspecialchars($user['address_line2']); ?><br>
                    <?php echo htmlspecialchars($user['city']) . ', ' . htmlspecialchars($user['state']) . ' ' . htmlspecialchars($user['postal_code']); ?><br>
                    <?php echo htmlspecialchars($user['country']); ?>
                </li>
                <li><strong>Account Created:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></li>
                <li><strong>Last Login:</strong> <?php echo $user['last_login'] ? date('F j, Y', strtotime($user['last_login'])) : 'Never logged in'; ?></li>
                <li><strong>Status:</strong> <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?></li>
            </ul>
        </div>
    </div>

    <!-- User's Orders Section -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Orders History</h5>
            <?php
            // Fetching orders made by this user
            $orders_query = "SELECT * FROM Orders WHERE user_id = $user_id ORDER BY order_date DESC";
            $orders = $conn->query($orders_query);

            if ($orders->num_rows > 0) {
                echo '<table class="table table-striped">';
                echo '<thead><tr><th>Order ID</th><th>Date</th><th>Status</th><th>Total Amount</th><th>Actions</th></tr></thead><tbody>';
                
                while ($order = $orders->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($order['order_id']) . '</td>';
                    echo '<td>' . date('F j, Y', strtotime($order['order_date'])) . '</td>';
                    echo '<td>' . htmlspecialchars($order['status']) . '</td>';
                    echo '<td>' . number_format($order['total_amount'], 2) . '</td>';
                    echo '<td><a href="http://localhost/bookstore/admin/orders/view.php?id=' . $order['order_id'] . '" class="btn btn-info">View</a></td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
            } else {
                echo '<p>No orders found for this user.</p>';
            }
            ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
