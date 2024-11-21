<?php

include '../../functions.php'; // Include your functions.php for database access and session management
guardDashboard(); // Ensure the user is logged in

// Define page URLs for the sidebar
$dashboardPage = '../dashboard.php';   // Adjust the path to the dashboard
$addSubjectPage = '../subject/add.php'; // Path to the 'add subject' page (relative to the current file)
$logoutPage = '../logout.php';   // Path for logging out (adjusted)

include '../partials/header.php'; 
include '../partials/side-bar.php';

$errorMessage = ''; 

$studentID = '';  // Default value if not set
$firstName = '';  // Default value if not set
$lastName = '';   // Default value if not set

// Handle form submission for registering a student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = trim($_POST['student_id']);
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);

    // Basic validation
    if (empty($studentID) || empty($firstName) || empty($lastName)) {
        $errorMessage = 'All fields are required.';
    } else {
        // Proceed to add the student to the database
        $conn = getConnection();
        $query = "INSERT INTO students (student_id, first_name, last_name) VALUES (:student_id, :first_name, :last_name)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':student_id', $studentID);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);

        if ($stmt->execute()) {
            // Redirect with a success parameter if the student was added
            header("Location: register.php?success=true");
            exit;
        } else {
            $errorMessage = 'Failed to add the student. Please try again.';
        }
    }
}

?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h3" style="font-weight: normal;">Register New Student</h1> 
    <br> <br>
            
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Register Student</li>
        </ol>
    </nav>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger mt-3">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="alert alert-success mt-3">
            Student registered successfully!
        </div>
    <?php endif; ?>

    <!-- Registration Form -->
    <div class="card p-4 mb-5">
        <form method="POST">
            <div class="mb-3 form-floating">
                <input type="text" class="form-control" id="student_id" name="student_id" 
                        value="<?= htmlspecialchars($studentID, ENT_QUOTES, 'UTF-8') ?>" 
                        placeholder="Student ID">
                <label for="student_id">Student ID</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="text" class="form-control" id="first_name" name="first_name" 
                        value="<?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?>" 
                        placeholder="First Name">
                <label for="first_name">First Name</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="text" class="form-control" id="last_name" name="last_name" 
                        value="<?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8') ?>" 
                        placeholder="Last Name">
                <label for="last_name">Last Name</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Student</button>
        </form>
    </div>

    <!-- Student List Table -->
    <div class="card p-4">
        <h3 class="card-title text-left">Student List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Fetch all students from the database
            $conn = getConnection();
            $stmt = $conn->query("SELECT * FROM students");
            $allStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($allStudents)):
                foreach ($allStudents as $student):
            ?>
                <tr>
                    <td><?= htmlspecialchars($student['student_id']) ?></td>
                    <td><?= htmlspecialchars($student['first_name']) ?></td>
                    <td><?= htmlspecialchars($student['last_name']) ?></td>
                    <td>
                        <!-- Edit Option -->
                        <a href="edit.php?student_id=<?= urlencode($student['student_id']) ?>" class="btn btn-info btn-sm">Edit</a>

                        <!-- Remove Option -->
                        <a href="delete.php?student_id=<?= urlencode($student['student_id']) ?>" class="btn btn-danger btn-sm">Delete</a>

                        <!-- Attach Subject Option -->
                        <a href="attach_subject.php?student_id=<?= urlencode($student['student_id']) ?>" class="btn btn-warning btn-sm">Attach Subject</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No students found.</td>
            </tr>
        <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../partials/footer.php'; ?>

