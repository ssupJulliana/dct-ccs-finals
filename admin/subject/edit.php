<?php

include '../../functions.php'; // Include your functions.php for database access and session management
guardDashboard(); // Ensure the user is logged in

// Define page URLs for the sidebar
$dashboardPage = '../dashboard.php';
$addSubjectPage = './add.php'; // Path to the 'add subject' page
$logoutPage = '../logout.php'; // Path for logging out

include '../partials/header.php'; 
include '../partials/side-bar.php';

$errorMessage = '';
$subjectCode = '';  // Default value if not set
$subjectName = '';  // Default value if not set

// Check if a subject_code is passed via GET
if (isset($_GET['subject_code'])) {
    $subjectCode = $_GET['subject_code'];

    // Fetch the subject details to show to the user for editing
    $subjectDetails = getSubjectByCode($subjectCode);

    if (!$subjectDetails) {
        // If no such subject found, redirect back to the add page
        header("Location: $addSubjectPage");
        exit;
    }

    // Set the subject name and subject code for the form fields
    $subjectName = $subjectDetails['subject_name'];
}

// Handle form submission for updating the subject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectCode = trim($_POST['subject_code']);
    $subjectName = trim($_POST['subject_name']);

    // Basic validation
    if (empty($subjectCode) || empty($subjectName)) {
        $errorMessage = 'Both subject code and subject name are required.';
    } else {
        // Proceed to update the subject in the database
        $conn = getConnection();
        $query = "UPDATE subjects SET subject_name = :subject_name WHERE subject_code = :subject_code";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':subject_code', $subjectCode);
        $stmt->bindParam(':subject_name', $subjectName);

        if ($stmt->execute()) {
            // Redirect with a success parameter if the subject was updated
            header("Location: edit.php?subject_code=$subjectCode&success=true");
            exit;
        } else {
            $errorMessage = 'Failed to update the subject. Please try again.';
        }
    }
}

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
