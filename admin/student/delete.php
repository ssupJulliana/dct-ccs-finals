<?php
include '../../functions.php'; // Include functions.php for database access and session management
guardDashboard(); // Ensure the user is logged in

// Define page URLs for the sidebar
$dashboardPage = '../dashboard.php';
$registerStudentPage = 'register.php';  // Path to the 'register student' page
$logoutPage = '../logout.php';  // Path for logging out

// Check if a student_id is passed via GET
if (isset($_GET['student_id'])) {
    $studentID = $_GET['student_id'];

    // Fetch student details based on the student_id
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $studentID);
    $stmt->execute();
    $studentDetails = $stmt->fetch();

    if (!$studentDetails) {
        // If no such student found, redirect back to the register student page
        header("Location: $registerStudentPage");
        exit;
    }

    // Handle the deletion process
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $deleteQuery = "DELETE FROM students WHERE student_id = :student_id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':student_id', $studentID);

        if ($deleteStmt->execute()) {
            // Redirect back to the register student page after successful deletion
            header("Location: $registerStudentPage?deleted=true");
            exit;
        } else {
            $errorMessage = "Failed to delete the student. Please try again.";
        }
    }
} else {
    // If no student_id is passed, redirect back to the register student page
    header("Location: $registerStudentPage");
    exit;
}

include '../partials/header.php';
include '../partials/side-bar.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h3">Delete Student</h1>
    <br> <br>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $dashboardPage; ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= $registerStudentPage; ?>">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
        </ol>
    </nav>

    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger mt-3">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <div class="card p-5 mb-5">
        <h5 class="card-title">Are you sure you want to delete the following student record?</h5>
        <ul>
            <li><strong>Student ID:</strong> <?= htmlspecialchars($studentDetails['student_id']) ?></li>
            <li><strong>First Name:</strong> <?= htmlspecialchars($studentDetails['first_name']) ?></li>
            <li><strong>Last Name:</strong> <?= htmlspecialchars($studentDetails['last_name']) ?></li>
        </ul>

        <!-- Form to delete the student -->
        <form method="POST">
        <div class="d-flex justify-content-start gap-1">
            <a href="<?= $registerStudentPage; ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Delete Student Record</button>
        </div>
        </form>
    </div>
</main>

<?php include '../partials/footer.php'; ?>
