<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Handle form submission
$name = $description = $parent_category_id = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $parent_category_id = !empty($_POST['parent_category_id']) ? (int)$_POST['parent_category_id'] : null;

    // Validate
    if (empty($name)) {
        $errors[] = "Category name is required.";
    }

    // Insert
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO Categories (name, description, parent_category_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $description, $parent_category_id);
        $stmt->execute();

        header("Location: list.php?success=1");
        exit;
    }
}

// Fetch categories for parent dropdown
$category_result = $conn->query("SELECT category_id, name FROM Categories ORDER BY name ASC");
$categories = $category_result->fetch_all(MYSQLI_ASSOC);

// Layout
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <h1 class="mb-4">Add New Category</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name *</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="parent_category_id" class="form-label">Parent Category</label>
                    <select class="form-select" id="parent_category_id" name="parent_category_id">
                        <option value="">-- None --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo ($parent_category_id == $cat['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Category
                </button>
                <a href="list.php" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
