<?php
session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: http://localhost/bookstore/login.php");
    exit;
}

require_once 'C:/xampp/htdocs/bookstore/user/config/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT is_active FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['is_active'] != 1) {
    session_unset();
    session_destroy();
    header("Location: http://localhost/bookstore/login.php");
    exit;
}
