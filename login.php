<?php
session_start();

include 'config/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['username'];
    $password = $_POST['password'];

    // 🔥 PREMADE ADMIN LOGIN (BYPASS DATABASE)
    if ($email === "admin@gmail.com" && $password === "12345") {

        $_SESSION['user'] = "admin";
        $_SESSION['role'] = "admin";

        header("Location: admin/index.php");
        exit();
    }

    // NORMAL DATABASE LOGIN (OTHER USERS)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['password'] === $password) {

        $_SESSION['user'] = $user['email'];
        $_SESSION['role'] = "user";

        header("Location: admin/index.php");
        exit();

    } else {
        $message = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>

<link rel="stylesheet" href="login-assets/css/style.css">
<link rel="icon" type="image/png" href="/admin/assets/log.png">

</head>
<body>

<div class="overlay">
    <div class="glass-card">

        <h2>Welcome to TaliResQ</h2>

        <?php if($message != ""): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">

            <label>Email</label>
            <input type="text" name="username" placeholder="Enter your email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>

        </form>

        <p class="register-text">
            Don't have an account? <a href="#">Register Here</a>
        </p>

    </div>
</div>

</body>
</html>