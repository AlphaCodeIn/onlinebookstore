<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/list_functions.php';

// Configuration
$entity = 'payments';
$entity_title = 'Payments';
$id_field = 'payment_id';

// Define table columns
$columns = [
    ['field' => 'payment_id', 'label' => 'Payment ID'],
    ['field' => 'order_id', 'label' => 'Order ID'],
    ['field' => 'amount', 'label' => 'Amount', 'format' => 'currency'],
    ['field' => 'payment_method', 'label' => 'Method'],
    ['field' => 'transaction_id', 'label' => 'Transaction ID'],
    ['field' => 'payment_date', 'label' => 'Date', 'format' => 'datetime'],
    [
        'field' => 'status',
        'label' => 'Status',
        'format' => 'status_badge',
        'options' => [
            'Completed' => 'success',
            'Pending' => 'warning',
            'Failed' => 'danger',
            'Refunded' => 'info'
        ]
    ],
];

// Pagination
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($current_page - 1) * $per_page;

// Filters
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build SQL conditions
$conditions = [];
$params = [];

if (!empty($status_filter)) {
    $conditions[] = "p.status = ?";
    $params[] = $status_filter;
}
if (!empty($search)) {
    $conditions[] = "(p.payment_id LIKE ? OR p.transaction_id LIKE ? OR p.payment_method LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

$where_clause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

// Query
$query = "SELECT p.* FROM Payments p $where_clause ORDER BY p.payment_date DESC LIMIT $per_page OFFSET $offset";
$count_query = "SELECT COUNT(*) as total FROM Payments p $where_clause";

// Execute query
if (!empty($params)) {
    $types = str_repeat('s', count($params));

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare($count_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total_items = $stmt->get_result()->fetch_assoc()['total'];
} else {
    $items = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
    $total_items = $conn->query($count_query)->fetch_assoc()['total'];
}

$total_pages = ceil($total_items / $per_page);

// Layout
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $entity_title; ?> Management</h1>
    </div>

    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Filter by Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="Completed" <?php echo $status_filter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Failed" <?php echo $status_filter === 'Failed' ? 'selected' : ''; ?>>Failed</option>
                            <option value="Refunded" <?php echo $status_filter === 'Refunded' ? 'selected' : ''; ?>>Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search payments..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
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
                                    No payments found
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
