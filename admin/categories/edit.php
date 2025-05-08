<?php
require_once '../config/db.php'; // adjust path if needed

// Get category ID from query string
$category_id = $_GET['id'] ?? null;

if (!$category_id) {
    die("Invalid request. No ID provided.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';

    if (!empty($name)) {
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE category_id = ?");
        $stmt->bind_param("si", $name, $category_id);

        if ($stmt->execute()) {
            header("Location: list.php?updated=1");
            exit;
        } else {
            $error = "Update failed: " . $stmt->error;
        }
    } else {
        $error = "Category name cannot be empty.";
    }
}

// Fetch existing category details
$stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    die("Category not found.");
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h1>Edit Category</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Category</button>
        <a href="list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
