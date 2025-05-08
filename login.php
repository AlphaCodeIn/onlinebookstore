<?php
session_start();
include './user/config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password_input = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password_input, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // update last login
            $conn->query("UPDATE users SET last_login = NOW() WHERE user_id = " . $user['user_id']);
            header("Location: ./user/index.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Online Bookstore</title>
    <style>
        <?php include 'user.css'; ?>
    </style>
</head>
<body>
<div class="form-container">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required autofocus>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
        <p>Don’t have an account? <a href="register.php">Register here</a>.</p>
    </form>
</div>
</body>
</html>
