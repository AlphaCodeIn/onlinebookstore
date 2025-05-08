<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BookNest - Your Online Bookstore</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

</head>
<body>
  <header>
    <div class="container">
      <nav class="navbar">
        <div id="logo-name">
        <a href="index.php" class="logo">ONLINE<span>BOOKSTORE</span></a>
        </div>
        
        <div class="nav-links">
          <a href="http://localhost/bookstore/user/index.php">Home</a>
          <a href="http://localhost/bookstore/user/books/browse.php">Books</a>
          <a href="#">About</a>
          <a href="#">Contact</a>
        </div>
        
        <div class="nav-icons">
          <a href="http://localhost/bookstore/user/wishlist/view.php" data-tooltip="Wishlist"><i class="fas fa-heart"></i></a>
          <a href="http://localhost/bookstore/user/cart/view.php" data-tooltip="Cart">
            <i class="fas fa-shopping-cart"></i>  
          </a>
          <?php if(isset($_SESSION['user_id'])): ?>
            <a href="profile.php" data-tooltip="Profile"><i class="fas fa-user"></i></a>
            <a href="logout.php" data-tooltip="Logout"><i class="fas fa-sign-out-alt"></i></a>
          <?php else: ?>
            <a href="login.php" data-tooltip="Login"><i class="fas fa-sign-in-alt"></i></a>
          <?php endif; ?>
        </div>
      </nav>
    </div>
  </header>

  <main>