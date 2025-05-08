<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: http://localhost/bookstore/admin/login.php');
    exit;
}


$admin_username = 'admin';
$admin_password_hash = password_hash('admin123', PASSWORD_DEFAULT);

function verifyAdminLogin($username, $password) {
    global $admin_username, $admin_password_hash;
    
    if ($username === $admin_username && password_verify($password, $admin_password_hash)) {
        return true;
    }
    return false;
}
?>