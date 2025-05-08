<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/list_functions.php';

// Configuration
$entity = 'categories';
$entity_title = 'Categories';
$add_url = 'add.php';
$id_field = 'category_id';

// Table columns
$columns = [
    ['field' => 'category_id', 'label' => 'ID'],
    ['field' => 'name', 'label' => 'Name'],
    ['field' => 'description', 'label' => 'Description'],
];

// Pagination
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($current_page - 1) * $per_page;

// Search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_condition = $search ? 
    "WHERE name LIKE '%$search%' OR description LIKE '%$search%'" 
    : '';

// Query
$query = "SELECT * FROM Categories $search_condition ORDER BY name ASC LIMIT $per_page OFFSET $offset";
$count_query = "SELECT COUNT(*) as total FROM Categories $search_condition";

$items = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
$total_items = $conn->query($count_query)->fetch_assoc()['total'];
$total_pages = ceil($total_items / $per_page);

// Layout includes
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $entity_title; ?> Management</h1>
        <a href="<?php echo $add_url; ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Category
        </a>
    </div>

    <!-- Search box -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="">
                <div class="input-group">
                    <input type="text" class="form-control" name="search"
                           placeholder="Search categories..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
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
                                    No categories found
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
