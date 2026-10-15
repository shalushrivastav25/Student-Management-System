
<?php
session_start();

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

$message = "";
$messageType = "";

$name = "";
$roll_no = "";
$email = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $roll_no = trim($_POST["roll_no"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($name === "" || $roll_no === "" || $email === "") {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif (!ctype_digit($roll_no) || (int)$roll_no < 1) {
        $message = "Roll number must be a positive number.";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO students (name, roll_no, email) VALUES (?, ?, ?)"
        );

        $rollNumber = (int)$roll_no;
        $stmt->bind_param("sis", $name, $rollNumber, $email);

        if ($stmt->execute()) {
            $message = "Student registered successfully!";
            $messageType = "success";
            $name = "";
            $roll_no = "";
            $email = "";
        } else {
            $message = "Unable to register student. Please check the database.";
            $messageType = "error";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php">Dashboard</a>
    <a href="register.php" class="active">Register Student</a>
    <a href="logout.php" class="logout-link">Logout</a>
</nav>

<div class="container">
    <h1>Student Registration</h1>
    <p>Enter the student's details in the form below.</p>

    <?php if ($message !== ""): ?>
        <div class="message <?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <div class="form-group">
            <label for="name">Student Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars($name); ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="roll_no">Roll Number</label>
            <input
                type="number"
                id="roll_no"
                name="roll_no"
                min="1"
                value="<?php echo htmlspecialchars($roll_no); ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($email); ?>"
                required
            >
        </div>

        <button type="submit" class="btn">Register Student</button>
    </form>

    <p><a href="index.php">Go to Dashboard</a></p>
</div>

</body>
</html>
