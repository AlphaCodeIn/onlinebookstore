<?php
include './user/config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, password, email, first_name, last_name, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $password, $_POST['password'], $email, $first_name, $last_name, $phone);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        $errors[] = "Username or email already exists.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Online Bookstore</title>
    <style>
        <?php include 'user.css'; ?>
    </style>
</head>
<body>
<div class="form-container">
    <h2>Create Account</h2>
    <?php foreach ($errors as $error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endforeach; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="tel" name="phone" placeholder="Phone Number">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Register</button>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</div>
</body>
</html>
