<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/edit_functions.php';

// Get the publisher ID from the URL
$publisher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($publisher_id <= 0) {
    // Redirect if no valid ID is provided
    header("Location: list.php");
    exit();
}

// Fetch publisher details from the database
$query = "SELECT * FROM Publishers WHERE publisher_id = $publisher_id LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    // Redirect to the list page if the publisher is not found
    header("Location: list.php");
    exit();
}

$publisher = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $website = isset($_POST['website']) ? trim($_POST['website']) : '';

    // Basic validation
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    if (empty($email)) {
        $errors[] = 'Email is required';
    }

    // If no errors, update the publisher
    if (empty($errors)) {
        $update_query = "UPDATE Publishers SET name = ?, address = ?, phone = ?, email = ?, website = ? WHERE publisher_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('sssssi', $name, $address, $phone, $email, $website, $publisher_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Publisher updated successfully!";
            header("Location: view.php?id=$publisher_id");
            exit();
        } else {
            $errors[] = "Failed to update publisher";
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Publisher</h1>
        <a href="view.php?id=<?php echo $publisher['publisher_id']; ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to View
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($publisher['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($publisher['address']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($publisher['phone']); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($publisher['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="website" class="form-label">Website</label>
                    <input type="text" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($publisher['website']); ?>">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
