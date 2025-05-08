<?php
require_once '../config/db.php'; // Database connection
require_once '../includes/auth.php'; // Authentication check
require_once '../config/delete_helper.php'; // Helper functions for deletion

// Ensure the request method is POST and 'id' parameter exists
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $user_id = (int) $_POST['id'];  // Sanitize and cast the ID to an integer

    // Call deleteEntity function to delete the user from the database
    $result = deleteEntity('Users', 'user_id', $user_id);

    // Check if the deletion was successful
    if ($result === true) {
        $_SESSION['message'] = "User has been successfully deleted.";  // Success message
        header("Location: list.php");  // Redirect to user list
        exit();
    } else {
        $_SESSION['error'] = $result;  // Error message
    }
} else {
    $_SESSION['error'] = "Invalid request.";  // Invalid request error
}

// Redirect to index if the request is not valid
header("Location: list.php");
exit();
?>
