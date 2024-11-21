<?php

include '../../functions.php';
guardDashboard(); // // Include your functions.php for database access and session management
// Ensure the user is logged in

// Define page URLs for the sidebar
$dashboardPage = '../dashboard.php';
$addSubjectPage = './add.php'; // Path to the 'add subject' page
$logoutPage = '../logout.php'; // Path for logging out



$errorMessage = '';
$subjectCode = '';  // Default value if not set
$subjectName = '';  // Default value if not set

// Check if a subject_code is passed via GET
if (isset($_GET['subject_code'])) {
    $subjectCode = $_GET['subject_code'];
    $subjectDetails = getSubjectByCode($subjectCode);
    if ($subjectDetails) {
        $subjectName = $subjectDetails['subject_name'];
    } else {
        // If subject is not found, redirect to add.php (or handle the error accordingly)
        header("Location: add.php");
        exit();
    }
} else {
    // If no subject code is provided, redirect to add.php
    header("Location: add.php");
    exit();
}


// Handle form submission for updating the subject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectCode = trim($_POST['subject_code']);
    $subjectName = trim($_POST['subject_name']);

    // Basic validation
    if (!empty($subjectCode) && !empty($subjectName)) {
        // Update subject in the database
        if (updateSubject($subjectCode, $subjectName)) {
            // If update is successful, redirect to add.php
            header("Location: add.php");
            exit();
        }
    }
}

include '../partials/header.php'; 
include '../partials/side-bar.php';

?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h3" style="font-weight: normal;">Edit Subject</h1>
    <br><br>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $dashboardPage; ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= $addSubjectPage; ?>">Add Subject</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
        </ol>
    </nav>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger mt-3">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="alert alert-success mt-3">
            Subject updated successfully!
        </div>
    <?php endif; ?>

    <!-- Card for Edit Subject Form -->
    <div class="card p-5 mb-5">
        <form method="POST">
            <div class="mb-3 form-floating">
                <input type="text" class="form-control" id="subject_code" name="subject_code" 
                    value="<?= htmlspecialchars($subjectCode, ENT_QUOTES, 'UTF-8') ?>" 
                    placeholder="Subject Code" readonly>
                <label for="subject_code">Subject Code</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" class="form-control" id="subject_name" name="subject_name" 
                    value="<?= htmlspecialchars($subjectName, ENT_QUOTES, 'UTF-8') ?>" 
                    placeholder="Subject Name">
                <label for="subject_name">Subject Name</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Subject</button>
        </form>
    </div>
</main>

<?php include '../partials/footer.php'; ?>
