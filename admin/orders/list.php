<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/list_functions.php';

// Configuration for orders
$entity = 'orders';
$entity_title = 'Orders';
$id_field = 'order_id'; // Primary key field name

// Define table columns
$columns = [
    ['field' => 'order_id', 'label' => 'Order ID'],
    [
        'field' => 'user_id', 
        'label' => 'Customer',
        'format' => 'customer_name',
        'first_name_field' => 'first_name',
        'last_name_field' => 'last_name'
    ],
    ['field' => 'order_date', 'label' => 'Order Date', 'format' => 'datetime'],
    ['field' => 'total_amount', 'label' => 'Amount', 'format' => 'currency'],
    [
        'field' => 'status', 
        'label' => 'Status',
        'format' => 'status_badge',
        'options' => [
            'Pending' => 'warning',
            'Processing' => 'info',
            'Shipped' => 'primary',
            'Delivered' => 'success',
            'Cancelled' => 'danger'
        ]
    ],
    ['field' => 'payment_method', 'label' => 'Payment Method']
];

// Pagination
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($current_page - 1) * $per_page;

// Filter handling
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build conditions
$conditions = [];
$params = [];

if (!empty($status_filter)) {
    $conditions[] = "o.status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $conditions[] = "(o.order_id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// Base query
$query = "SELECT o.*, u.first_name, u.last_name 
          FROM Orders o
          JOIN Users u ON o.user_id = u.user_id
          $where_clause
          ORDER BY o.order_date DESC 
          LIMIT $per_page OFFSET $offset";

$count_query = "SELECT COUNT(*) as total 
                FROM Orders o
                JOIN Users u ON o.user_id = u.user_id
                $where_clause";

// Prepare and execute query with parameters if needed
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $count_result = $stmt->get_result();
    $total_items = $count_result->fetch_assoc()['total'];
} else {
    $items = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
    $total_items = $conn->query($count_query)->fetch_assoc()['total'];
}

$total_pages = ceil($total_items / $per_page);

// Include header and sidebar
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $entity_title; ?> Management</h1>
        <div>
            <a href="export.php" class="btn btn-secondary me-2">
                <i class="fas fa-file-export"></i> Export
            </a>
        </div>
    </div>

    <!-- Filter and Search Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Filter by Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Processing" <?php echo $status_filter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="Shipped" <?php echo $status_filter === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="Delivered" <?php echo $status_filter === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Cancelled" <?php echo $status_filter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search orders..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <?php echo buildTableHeaders($columns); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="<?php echo count($columns) + 1; ?>" class="text-center py-4">
                                    No orders found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <?php echo buildTableRow($item, $columns, $entity, $id_field); ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <?php 
                $query_params = $_GET;
                unset($query_params['page']);
                echo buildPagination($current_page, $total_pages, $query_params); 
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>