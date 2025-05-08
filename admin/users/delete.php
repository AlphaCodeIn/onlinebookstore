<?php
// Include necessary files
require_once '../includes/auth.php';
require_once '../config/db.php';

// Check if ID is provided in the URL
if (isset($_POST['id'])) {
    // Get the user ID to delete
    $user_id = $_POST['id'];

    // Prepare the delete query
    $query = "DELETE FROM users WHERE user_id = ?";

    // Prepare statement
    if ($stmt = $conn->prepare($query)) {
        // Bind the user_id parameter
        $stmt->bind_param("i", $user_id);

        // Execute the delete query
        if ($stmt->execute()) {
            // Redirect back to list page with success message
            header('Location: list.php?success=1');
            exit;
        } else {
            echo "Error: Could not delete user.";
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Error: Could not prepare statement.";
    }
} else {
    // Redirect to list if no ID is provided
    header('Location: list.php');
    exit;
}

// Close the database connection
$conn->close();
?>
