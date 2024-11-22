<?php

include '../../functions.php'; 
guardDashboard(); // Ensure the user is logged in

// Define page URLs for the sidebar
$dashboardPage = '../dashboard.php';
$registerStudentPage = './register.php'; // Path to the 'register student' page
$logoutPage = '../logout.php'; // Path for logging out

$errorMessage = '';
$studentID = '';  // Default value if not set
$firstName = '';  // Default value if not set
$lastName = '';  // Default value if not set

// Check if a student_id is passed via GET
if (isset($_GET['student_id'])) {
    $studentID = $_GET['student_id'];
    
    // Fetch student details from the database
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $studentID);
    $stmt->execute();
    
    $studentDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($studentDetails) {
        // Populate the form fields with the student's existing data
        $firstName = $studentDetails['first_name'];
        $lastName = $studentDetails['last_name'];
    } else {
        // If student is not found, redirect to register.php
        header("Location: $registerStudentPage");
        exit();
    }
} else {
    // If no student_id is provided, redirect to register.php
    header("Location: $registerStudentPage");
    exit();
}

// Handle form submission for updating the student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = trim($_POST['student_id']);
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);

    // Basic validation
    if (!empty($studentID) && !empty($firstName) && !empty($lastName)) {
        // Update student in the database
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE students SET first_name = :first_name, last_name = :last_name WHERE student_id = :student_id");
        $stmt->bindParam(':student_id', $studentID);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);

        if ($stmt->execute()) {
            // If update is successful, redirect to register.php
            header("Location: $registerStudentPage?success=true");
            exit();
        } else {
            $errorMessage = "Failed to update the student. Please try again.";
        }
    } else {
        $errorMessage = "All fields are required.";
    }
}

include '../partials/header.php'; 
include '../partials/side-bar.php';

?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h3" style="font-weight: normal;">Edit Student</h1>
    <br><br>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $dashboardPage; ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= $registerStudentPage; ?>">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
        </ol>
    </nav>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger mt-3">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="alert alert-success mt-3">
            Student updated successfully!
        </div>
    <?php endif; ?>

    <!-- Card for Edit Student Form -->
    <div class="card p-5 mb-5">
        <form method="POST">
            <div class="mb-3 form-floating">
                <input type="text" class="form-control" id="student_id" name="student_id" 
                    value="<?= htmlspecialchars($studentID, ENT_QUOTES, 'UTF-8') ?>" 
                    placeholder="Student ID" readonly>
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

            <button type="submit" class="btn btn-primary w-100">Update Student</button>
        </form>
    </div>
</main>

<?php include '../partials/footer.php'; ?>
