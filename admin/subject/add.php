<?php

include '../../functions.php'; // Include your functions.php for database access and session management
guardDashboard(); // Ensure the user is logged in

// Define page URLs for the sidebar
$dashboardPage = '../dashboard.php';   // Adjust the path to the dashboard// Path to the 'add subject' page (relative to the current file)
$registerStudentPage = '../student/register.php';  // Path to the 'register student' page (adjusted)
$logoutPage = '../logout.php';   // Path for logging out (adjusted)

include '../partials/header.php'; 
include '../partials/side-bar.php';

$errorMessage = '';

$subjectCode = '';  // Default value if not set
$subjectName = '';  // Default value if not set



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
    <h1 class="h3" style="font-weight: normal;">Add New Subject</h1> 
    <br> <br>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
                </ol>
            </nav>


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

    
        <div>
        <div class="card p-4 mb-5">
        <form method="POST">
            <div class="mb-3 form-floating">
            <input type="text" class="form-control" id="subject_code" name="subject_code" 
                        value="<?= htmlspecialchars($subjectCode, ENT_QUOTES, 'UTF-8') ?>" 
                        placeholder="Subject Code">
                        <label for="subject_code">Subject Code</label>
    </div>
        <div class="mb-3 form-floating">
        <input type="text" class="form-control" id="subject_name" name="subject_name" 
                        value="<?= htmlspecialchars($subjectName, ENT_QUOTES, 'UTF-8') ?>" 
                        placeholder="Subject Name">
                        <label for="subject_name">Subject Name</label>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Subject</button>
    </div>

 <!-- Subject List Table -->
 <div class="card p-4">
        <h3 class="card-title text-left">Subject List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $allSubjects = fetchSubjects();
            if (!empty($allSubjects)):
                foreach ($allSubjects as $subjectDetails):
            ?>
                <tr>
                    <td><?= htmlspecialchars($subjectDetails['subject_code']) ?></td>
                    <td><?= htmlspecialchars($subjectDetails['subject_name']) ?></td>
                    <td>
                        <!-- Edit Option -->
                        <a href="edit.php?subject_code=<?= urlencode($subjectDetails['subject_code']) ?>" class="btn btn-info btn-sm">Edit</a>

                        <!-- Remove Option -->
                        <a href="delete.php?subject_code=<?= urlencode($subjectDetails['subject_code']) ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No subjects found.</td>
            </tr>
        <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php include
'../partials/footer.php'; ?>


