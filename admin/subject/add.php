<?php
require '../../functions.php'; // Include your functions.php for database access and session management
guardDashboard(); // Ensure the user is logged in

// Define page URLs for the sidebar
$dashboardPage = '../dashboard.php';   // Adjust the path to the dashboard
$addSubjectPage = './add.php';  // Path to the 'add subject' page (relative to the current file)
$registerStudentPage = '../student/register.php';  // Path to the 'register student' page (adjusted)
$logoutPage = '../logout.php';   // Path for logging out (adjusted)

require '../partials/header.php'; 
require '../partials/side-bar.php';
?>


<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Add New Subject</h1>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger mt-3">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="alert alert-success mt-3">
            Subject added successfully!
        </div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="subject_name" class="form-label">Subject Name</label>
            <input type="text" class="form-control" id="subject_name" name="subject_name" required>
        </div>
        <div class="mb-3">
            <label for="subject_description" class="form-label">Subject Description</label>
            <textarea class="form-control" id="subject_description" name="subject_description" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Subject</button>
    </form>
</main>

<?php require './partials/footer.php'; ?>


