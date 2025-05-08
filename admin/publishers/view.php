<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/list_functions.php';

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

// Include header and sidebar
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>View Publisher Details</h1>
        <a href="list.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($publisher['name']); ?></h5>
            <p class="card-text"><strong>Address:</strong> <?php echo htmlspecialchars($publisher['address']); ?></p>
            <p class="card-text"><strong>Phone:</strong> <?php echo htmlspecialchars($publisher['phone']); ?></p>
            <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($publisher['email']); ?></p>
            <p class="card-text"><strong>Website:</strong> <a href="<?php echo htmlspecialchars($publisher['website']); ?>" target="_blank"><?php echo htmlspecialchars($publisher['website']); ?></a></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
