
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

/* Get search text from the search box */
$search = trim($_GET["q"] ?? "");

/* Handle update and delete operations */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "update") {
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        $name = trim($_POST["name"] ?? "");
        $roll_no = trim($_POST["roll_no"] ?? "");
        $email = trim($_POST["email"] ?? "");

        if (!$id || $name === "" || $roll_no === "" || $email === "") {
            $message = "Please fill in all fields correctly.";
            $messageType = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter a valid email address.";
            $messageType = "error";
        } elseif (!ctype_digit($roll_no) || (int)$roll_no < 1) {
            $message = "Roll number must be a positive number.";
            $messageType = "error";
        } else {
            $rollNumber = (int)$roll_no;

            $stmt = $conn->prepare(
                "UPDATE students SET name = ?, roll_no = ?, email = ?
                 WHERE id = ?"
            );
            $stmt->bind_param("sisi", $name, $rollNumber, $email, $id);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: index.php?status=updated");
                exit;
            }

            $message = "Unable to update student.";
            $messageType = "error";
            $stmt->close();
        }
    }

    if ($action === "delete") {
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

        if ($id) {
            $stmt = $conn->prepare(
                "DELETE FROM students WHERE id = ?"
            );
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: index.php?status=deleted");
                exit;
            }

            $message = "Unable to delete student.";
            $messageType = "error";
            $stmt->close();
        } else {
            $message = "Invalid student ID.";
            $messageType = "error";
        }
    }
}

/* Display success messages */
if (isset($_GET["status"])) {
    if ($_GET["status"] === "updated") {
        $message = "Student updated successfully!";
        $messageType = "success";
    } elseif ($_GET["status"] === "deleted") {
        $message = "Student deleted successfully!";
        $messageType = "success";
    }
}

/* Get the student selected for editing */
$editStudent = null;

if (isset($_GET["edit"]) && ctype_digit((string)$_GET["edit"])) {
    $editId = (int)$_GET["edit"];

    $stmt = $conn->prepare(
        "SELECT id, name, roll_no, email
         FROM students WHERE id = ?"
    );
    $stmt->bind_param("i", $editId);
    $stmt->execute();

    $result = $stmt->get_result();
    $editStudent = $result->fetch_assoc();
    $stmt->close();

    if (!$editStudent) {
        $message = "Student not found.";
        $messageType = "error";
    }
}

/* Dashboard summary cards */
$summaryResult = $conn->query(
    "SELECT
        COUNT(*) AS total_students,
        COUNT(DISTINCT roll_no) AS unique_roll_numbers,
        SUM(CASE
            WHEN email IS NOT NULL AND email <> '' THEN 1
            ELSE 0
        END) AS students_with_email
     FROM students"
);

$summary = $summaryResult->fetch_assoc();

/* Search by name, roll number, or email */
if ($search !== "") {
    $searchTerm = "%" . $search . "%";

    $stmt = $conn->prepare(
        "SELECT id, name, roll_no, email
         FROM students
         WHERE name LIKE ?
            OR CAST(roll_no AS CHAR) LIKE ?
            OR email LIKE ?
         ORDER BY id ASC"
    );

    $stmt->bind_param(
        "sss",
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();
    $students = $stmt->get_result();
} else {
    $students = $conn->query(
        "SELECT id, name, roll_no, email
         FROM students
         ORDER BY id ASC"
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Management Dashboard</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navigation bar -->
<nav class="navbar">
    <a href="index.php" class="brand">
        <span class="brand-icon">S</span>
        <span>Student Manager</span>
    </a>

    <div class="nav-links">
        <a href="index.php" class="nav-link active">Dashboard</a>
        <a href="register.php" class="nav-link">Register Student</a>
        <a href="logout.php" class="nav-link logout-link">Logout</a>
    </div>
</nav>

<main class="container">

    <!-- Page heading -->
    <section class="page-heading">
        <div>
            <p class="eyebrow">STUDENT ADMINISTRATION</p>
            <h1>Dashboard</h1>
            <p class="subtitle">
                Manage, search, and organize your student records.
            </p>
        </div>

        <a href="register.php" class="btn">
            + Register Student
        </a>
    </section>

    <!-- Success or error message -->
    <?php if ($message !== ""): ?>
        <div class="message <?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Summary cards -->
    <section class="summary-grid">

        <div class="summary-card">
            <div class="summary-icon icon-blue">ST</div>
            <div>
                <p class="summary-label">Total Students</p>
                <h2>
                    <?php echo (int)$summary["total_students"]; ?>
                </h2>
                <p class="summary-note">All registered records</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon icon-purple">RN</div>
            <div>
                <p class="summary-label">Unique Roll Numbers</p>
                <h2>
                    <?php echo (int)$summary["unique_roll_numbers"]; ?>
                </h2>
                <p class="summary-note">Distinct roll numbers</p>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon icon-green">@</div>
            <div>
                <p class="summary-label">Students With Email</p>
                <h2>
                    <?php echo (int)$summary["students_with_email"]; ?>
                </h2>
                <p class="summary-note">Email addresses recorded</p>
            </div>
        </div>

    </section>

    <!-- Edit student form -->
    <?php if ($editStudent): ?>
        <section class="panel edit-panel">
            <div class="panel-heading">
                <div>
                    <h2>Edit Student</h2>
                    <p>Update the student's information below.</p>
                </div>
            </div>

            <form method="POST" action="index.php" class="student-form">
                <input type="hidden" name="action" value="update">

                <input type="hidden" name="id"
                    value="<?php echo (int)$editStudent["id"]; ?>">

                <div class="form-group">
                    <label for="edit-name">Student Name</label>
                    <input type="text" id="edit-name" name="name"
                        value="<?php echo htmlspecialchars($editStudent["name"]); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="edit-roll">Roll Number</label>
                    <input type="number" id="edit-roll" name="roll_no"
                        min="1"
                        value="<?php echo (int)$editStudent["roll_no"]; ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="edit-email">Email Address</label>
                    <input type="email" id="edit-email" name="email"
                        value="<?php echo htmlspecialchars($editStudent["email"]); ?>"
                        required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">
                        Save Changes
                    </button>

                    <a href="index.php" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <!-- Student records panel -->
    <section class="panel">

        <div class="panel-heading">
            <div>
                <h2>Student Records</h2>
                <p>View and manage your registered students.</p>
            </div>

            <span class="record-count">
                <?php echo $students->num_rows; ?>
                result(s)
            </span>
        </div>

        <!-- Search form -->
        <form method="GET" action="index.php" class="search-form">
            <div class="search-input-wrap">
                <span class="search-symbol">&#128269;</span>

                <input
                    type="search"
                    name="q"
                    placeholder="Search by name, roll number, or email..."
                    value="<?php echo htmlspecialchars($search); ?>"
                    aria-label="Search students"
                >
            </div>

            <button type="submit" class="btn">Search</button>

            <?php if ($search !== ""): ?>
                <a href="index.php" class="btn btn-secondary">
                    Clear
                </a>
            <?php endif; ?>
        </form>

        <!-- Students table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Roll Number</th>
                        <th>Email Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($students->num_rows > 0): ?>

                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="id-badge">
                                        #<?php echo (int)$student["id"]; ?>
                                    </span>
                                </td>

                                <td class="student-name">
                                    <?php echo htmlspecialchars($student["name"]); ?>
                                </td>

                                <td>
                                    <?php echo (int)$student["roll_no"]; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($student["email"]); ?>
                                </td>

                                <td class="action-cell">
                                    <a class="action-edit"
                                       href="index.php?edit=<?php echo (int)$student["id"]; ?>">
                                        Edit
                                    </a>

                                    <form method="POST" action="index.php"
                                        class="delete-form"
                                        onsubmit="return confirm('Are you sure you want to delete this student?');">

                                        <input type="hidden" name="action"
                                            value="delete">

                                        <input type="hidden" name="id"
                                            value="<?php echo (int)$student["id"]; ?>">

                                        <button type="submit" class="action-delete">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <?php if ($search !== ""): ?>
                                    No students found for
                                    "<?php echo htmlspecialchars($search); ?>".

                                    <br>
                                    <a href="index.php">Show all students</a>
                                <?php else: ?>
                                    No students registered yet.
                                    <br>
                                    <a href="register.php">Register your first student</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </section>

    <footer class="footer">
        Student Management System &middot; Built with PHP and MySQL
    </footer>

</main>

</body>
</html>
