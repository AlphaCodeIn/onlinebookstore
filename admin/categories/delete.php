<?php
require_once '../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: list.php?deleted=1");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Invalid ID";
    }
} else {
    echo "Invalid request method";
}
