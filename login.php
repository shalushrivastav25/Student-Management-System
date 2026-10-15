
<?php
session_start();

/*
   Demo admin credentials.
   Change these before using the project beyond a classroom demo.
*/
$adminUsername = "admin";
$adminPassword = "Admin@123";

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        hash_equals($adminUsername, $username) &&
        hash_equals($adminPassword, $password)
    ) {
        session_regenerate_id(true);
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"] = $adminUsername;

        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Student Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <main class="login-card">
        <div class="login-logo">S</div>

        <h1>Admin Login</h1>
        <p class="login-subtitle">
            Sign in to manage student records.
        </p>

        <?php if ($error !== ""): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="btn login-button">
                Login
            </button>
        </form>

        <p class="login-footer">Student Management System</p>
    </main>

</body>
</html>
