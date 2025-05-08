<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/list_functions.php';

// Configuration for users
$entity = 'users';
$entity_title = 'Edit User';
$id_field = 'user_id'; 


if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $query = "SELECT * FROM Users WHERE user_id = $user_id LIMIT 1";
    $user = $conn->query($query)->fetch_assoc();

    if (!$user) {
        header('Location: list.php');
        exit;
    }
} else {
    header('Location: list.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect the updated data
    $username = $conn->real_escape_string($_POST['username']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address_line1 = $conn->real_escape_string($_POST['address_line1']);
    $address_line2 = $conn->real_escape_string($_POST['address_line2']);
    $city = $conn->real_escape_string($_POST['city']);
    $state = $conn->real_escape_string($_POST['state']);
    $postal_code = $conn->real_escape_string($_POST['postal_code']);
    $country = $conn->real_escape_string($_POST['country']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Update user in the database
    $update_query = "
        UPDATE Users SET 
            username = '$username', 
            first_name = '$first_name',
            last_name = '$last_name', 
            email = '$email', 
            phone = '$phone', 
            address_line1 = '$address_line1',
            address_line2 = '$address_line2',
            city = '$city', 
            state = '$state', 
            postal_code = '$postal_code',
            country = '$country',
            is_active = '$is_active'
        WHERE user_id = $user_id
    ";

    if ($conn->query($update_query)) {
        header('Location: view.php?id=' . $user_id);
        exit;
    } else {
        $error_message = "Error updating user information.";
    }
}

// Include header and sidebar
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $entity_title; ?></h1>
        <a href="view.php?id=<?php echo $user[$id_field]; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to User Details
        </a>
    </div>

    <!-- Edit User Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Edit User Information</h5>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="address_line1">Address Line 1</label>
                        <input type="text" class="form-control" id="address_line1" name="address_line1" value="<?php echo htmlspecialchars($user['address_line1']); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="address_line2">Address Line 2</label>
                        <input type="text" class="form-control" id="address_line2" name="address_line2" value="<?php echo htmlspecialchars($user['address_line2']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="state">State</label>
                        <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($user['state']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code']); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($user['country']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="is_active">Status</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo $user['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
