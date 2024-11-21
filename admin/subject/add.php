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

$errorMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectCode = trim($_POST['subject_code']);
    $subjectName = trim($_POST['subject_name']);

    // Basic validation
    if (empty($subjectCode) || empty($subjectName)) {
        $errorMessage = 'Both subject name and description are required.';
    } else {
        // Proceed to add the subject to the database (you can adjust the query here)
        $conn = getConnection();
        $query = "INSERT INTO subjects (subject_code, subject_name) VALUES (:subject_code, :subject_name)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':subject_code', $subjectCode);
        $stmt->bindParam(':subject_name', $subjectName);

        if ($stmt->execute()) {
            // Redirect with a success parameter if the subject was added
            header("Location: add.php?success=true");
            exit;
        } else {
            $errorMessage = 'Failed to add the subject. Please try again.';
        }
    }
}

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
            <label for="subject_code" class="form-label">Subject Code</label>
            <input type="text" class="form-control" id="subject_code" name="subject_code" required>
        </div>
        <div class="mb-3">
            <label for="subject_name" class="form-label">Subject Name</label>
            <textarea class="form-control" id="subject_name" name="subject_name" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Subject</button>
    </form>
</main>

<?php require './partials/footer.php'; ?>


