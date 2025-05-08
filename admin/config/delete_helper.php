<?php
function deleteEntity($table, $primary_key, $id) {
    global $conn;

    // Validate if ID is numeric
    if (!is_numeric($id)) {
        return "Invalid ID provided.";
    }

    // Start a transaction to ensure both deletions are handled together
    $conn->begin_transaction();

    try {
        // First, delete the related orders (if any)
        $delete_orders_query = "DELETE FROM orders WHERE user_id = ?";
        if ($stmt = $conn->prepare($delete_orders_query)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }

        // Then, delete the user
        $delete_user_query = "DELETE FROM $table WHERE $primary_key = ?";
        if ($stmt = $conn->prepare($delete_user_query)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }

        // Commit the transaction
        $conn->commit();
        return true;  // Deletion successful
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        $conn->rollback();
        return "Failed to delete record: " . $e->getMessage();
    }
}
?>
