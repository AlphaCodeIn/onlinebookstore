<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/list_functions.php';

// Configuration for publishers
$entity = 'Publishers';
$entity_title = 'Publishers';
$add_url = 'add.php';
$id_field = 'publisher_id'; // Primary key field name

// Define table columns
$columns = [
    ['field' => 'publisher_id', 'label' => 'ID'],
    ['field' => 'name', 'label' => 'Name'],
    ['field' => 'address', 'label' => 'Address'],
    ['field' => 'phone', 'label' => 'Phone'],
    ['field' => 'email', 'label' => 'Email'],
    ['field' => 'website', 'label' => 'Website'],
];

// Pagination
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($current_page - 1) * $per_page;

// Search handling
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_condition = $search ? "WHERE name LIKE '%$search%' OR address LIKE '%$search%' OR phone LIKE '%$search%' OR email LIKE '%$search%' OR website LIKE '%$search%'" : '';

// Base query
$query = "SELECT * FROM Publishers $search_condition ORDER BY name ASC LIMIT $per_page OFFSET $offset";
$count_query = "SELECT COUNT(*) as total FROM Publishers $search_condition";

// Get data
$items = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
$total_items = $conn->query($count_query)->fetch_assoc()['total'];
$total_pages = ceil($total_items / $per_page);

// Include header and sidebar
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $entity_title; ?> List</h1>
        <a href="<?php echo $add_url; ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Publisher
        </a>
    </div>

    <!-- Search Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search publishers..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Publishers Table Card -->
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
                                <td colspan="<?php echo count($columns); ?>" class="text-center py-4">
                                    No publishers found
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
