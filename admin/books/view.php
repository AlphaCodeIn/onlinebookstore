<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$book_id = (int)$_GET['id'];

$query = "
    SELECT b.*, p.name AS publisher_name, c.name AS category_name
    FROM Books b
    LEFT JOIN Publishers p ON b.publisher_id = p.publisher_id
    LEFT JOIN Categories c ON b.category_id = c.category_id
    WHERE b.book_id = $book_id
    LIMIT 1
";

$result = $conn->query($query);
$book = $result->fetch_assoc();

if (!$book) {
    echo "<div class='alert alert-danger'>Book not found.</div>";
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>View Book</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card mb-4">
        <div class="row g-0">
            <div class="col-md-3 text-center p-4">
                <?php if (!empty($book['cover_image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($book['cover_image_url']); ?>" class="img-fluid" alt="Cover">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="width:100%; height:250px;">
                        <i class="fas fa-book fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <div class="card-body">
                    <h3 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="text-muted mb-1">ISBN: <?php echo htmlspecialchars($book['isbn']); ?></p>
                    <p class="mb-1"><strong>Author:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
                    <p class="mb-1"><strong>Publisher:</strong> <?php echo htmlspecialchars($book['publisher_name'] ?? 'N/A'); ?></p>
                    <p class="mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($book['category_name'] ?? 'N/A'); ?></p>
                    <p class="mb-1"><strong>Language:</strong> <?php echo htmlspecialchars($book['language'] ?? 'N/A'); ?></p>
                    <p class="mb-1"><strong>Publication Date:</strong> <?php echo htmlspecialchars($book['publication_date']); ?></p>
                    <p class="mb-1"><strong>Pages:</strong> <?php echo htmlspecialchars($book['pages']); ?></p>
                    <p class="mb-1"><strong>Price:</strong> ₹<?php echo number_format($book['price'], 2); ?></p>
                    <p class="mb-1"><strong>Stock:</strong> 
                        <span class="<?php echo $book['stock_quantity'] > 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo $book['stock_quantity']; ?>
                        </span>
                    </p>
                    <p class="mb-1">
                        <strong>Status:</strong>
                        <span class="badge bg-<?php echo $book['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $book['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                        <?php if ($book['is_featured']): ?>
                            <span class="badge bg-warning ms-1">Featured</span>
                        <?php endif; ?>
                    </p>
                    <p class="mt-3"><strong>Description:</strong><br>
                        <?php echo nl2br(htmlspecialchars($book['description'])); ?>
                    </p>
                    <p class="text-muted mt-3">
                        <small>Created at: <?php echo $book['created_at']; ?> | Updated at: <?php echo $book['updated_at'] ?? 'N/A'; ?></small>
                    </p>
                    <div class="mt-4">
                        <a href="edit.php?id=<?php echo $book['book_id']; ?>" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
