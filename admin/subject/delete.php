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

    $subjectDetails = getSubjectByCode($subjectCode);

    if (!$subjectDetails) {
        // If no such subject found, redirect back to the add page
        header("Location: $addSubjectPage");
        exit;
    }

    // Handle the deletion process
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteSubject($subjectCode)) {
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

    <div class="card p-5 mb-5">
        <h5 class="card-title">Are you sure you want to delete the following subject record?</h5>
        <ul>
            <li><strong>Subject Code:</strong> <?= htmlspecialchars($subjectDetails['subject_code']) ?></li>
            <li><strong>Subject Name:</strong> <?= htmlspecialchars($subjectDetails['subject_name']) ?></li>
        </ul>

        <!-- Form to delete the subject -->
        <form method="POST">
        <div class="d-flex justify-content-start gap-1">
             <a href="<?= $addSubjectPage; ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Delete Subject Record</button>
        </form>
    </div>
</main>

<?php include '../partials/footer.php'; ?>
