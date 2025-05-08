<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
// require_once '../config/book_functions.php';

// Pagination variables
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($current_page - 1) * $per_page;

// Search and filter handling
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Base query
$query = "SELECT b.*, p.name as publisher_name 
          FROM Books b
          LEFT JOIN Publishers p ON b.publisher_id = p.publisher_id
          WHERE 1=1";

// Apply filters
if (!empty($search)) {
    $query .= " AND (b.title LIKE '%$search%' OR b.author_name LIKE '%$search%' OR b.isbn LIKE '%$search%')";
}

if ($status_filter !== 'all') {
    $query .= " AND b.is_active = " . ($status_filter === 'active' ? '1' : '0');
}

// Get total count for pagination
$count_result = $conn->query(str_replace('b.*, p.name as publisher_name', 'COUNT(*) as total', $query));
$total_books = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $per_page);

// Add sorting and pagination
$query .= " ORDER BY b.created_at DESC LIMIT $per_page OFFSET $offset";

// Execute query
$result = $conn->query($query);
$books = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Book Management</h1>
        <a href="add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Book
        </a>
    </div>

    <!-- Search and Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search by title, author or ISBN..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <select class="form-select" name="status">
                                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Books</option>
                                <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active Only</option>
                                <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive Only</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Books Table Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Book List</h5>
            <small class="text-muted">Showing <?php echo count($books); ?> of <?php echo $total_books; ?> books</small>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Publisher</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($books)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">No books found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo $book['book_id']; ?></td>
                                    <td>
                                        <?php if (!empty($book['cover_image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($book['cover_image_url']); ?>" alt="Book Cover" style="width: 50px; height: auto;">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 65px;">
                                                <i class="fas fa-book text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($book['title']); ?></strong><br>
                                        <small class="text-muted">ISBN: <?php echo htmlspecialchars($book['isbn']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['author_name']); ?></td>
                                    <td><?php echo htmlspecialchars($book['publisher_name'] ?? 'N/A'); ?></td>
                                    <td>₹<?php echo number_format($book['price'], 2); ?></td>
                                    <td>
                                        <span class="<?php echo $book['stock_quantity'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $book['stock_quantity']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $book['is_active'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $book['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                        <?php if ($book['is_featured']): ?>
                                            <span class="badge bg-warning ms-1">Featured</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
    <div class="d-flex gap-2">
        <a href="view.php?id=<?php echo $book['book_id']; ?>" class="btn btn-sm btn-outline-primary" title="View">
            <i class="fas fa-eye"></i>
        </a>
        <a href="edit.php?id=<?php echo $book['book_id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <!-- Change this to point to delete.php -->
        <a href="delete.php?id=<?php echo $book['book_id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this book?')">
            <i class="fas fa-trash"></i>
        </a>
    </div>
</td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>