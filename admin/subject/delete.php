<?php
include '../../functions.php'; // Include functions.php for database access and session management
guardDashboard(); // Ensure the user is logged in

// Define page URLs for the sidebar
$dashboardPage = '../dashboard.php';
$addSubjectPage = './add.php'; // Path to the 'add subject' page
$registerStudentPage = '../student/register.php';  // Path to the 'register student' page
$logoutPage = '../logout.php';  // Path for logging out

// Check if a subject_code is passed via GET
if (isset($_GET['subject_code'])) {
    $subjectCode = $_GET['subject_code'];

    // Fetch the subject details to show to the user for confirmation
    $conn = getConnection();
    $query = "SELECT * FROM subjects WHERE subject_code = :subject_code";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':subject_code', $subjectCode);
    $stmt->execute();
    $subjectDetails = $stmt->fetch();

    if (!$subjectDetails) {
        // If no such subject found, redirect back to the add page
        header("Location: $addSubjectPage");
        exit;
    }

    // Handle the deletion process
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Deleting the subject from the database
        $deleteQuery = "DELETE FROM subjects WHERE subject_code = :subject_code";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':subject_code', $subjectCode);

        if ($deleteStmt->execute()) {
            // Redirect back to the add subject page after successful deletion
            header("Location: $addSubjectPage?deleted=true");
            exit;
        } else {
            $errorMessage = "Failed to delete the subject. Please try again.";
        }
    }
} else {
    // If no subject_code is passed, redirect back to the subject list page
    header("Location: $addSubjectPage");
    exit;
}

include '../partials/header.php';
include '../partials/side-bar.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h3">Delete Subject</h1>
    <br> <br>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $dashboardPage; ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= $addSubjectPage; ?>">Add Subject</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
        </ol>
    </nav>

    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger mt-3">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <div class="card p-4 mb-5">
        <h5 class="card-title">Are you sure you want to delete the following subject record?</h5>
        <table class="table">
            <tr>
                <th>Subject Code</th>
                <td><?= htmlspecialchars($subjectDetails['subject_code']) ?></td>
            </tr>
            <tr>
                <th>Subject Name</th>
                <td><?= htmlspecialchars($subjectDetails['subject_name']) ?></td>
            </tr>
        </table>

        <!-- Form to delete the subject -->
        <form method="POST">
            <button type="submit" class="btn btn-danger">Delete Subject Record</button>
            <a href="<?= $addSubjectPage; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</main>

<?php include '../partials/footer.php'; ?>
