<?php
if (session_status() == PHP_SESSION_NONE) {
session_start();   
} 
function postData($key){
    return $_POST["$key"];
}

function guardLogin(){
    
    $dashboardPage = 'admin/dashboard.php';

    if(isset($_SESSION['email'])){
        header("Location: $dashboardPage");
    } 
}

function guardDashboard(){
    $loginPage = '../index.php';
    if(!isset($_SESSION['email'])){
        header("Location: $loginPage");
    }
}


function getConnection() {
    // Database configuration
    $host = 'localhost'; // Replace with your host
    $dbName = 'dct-ccs-finals'; // Replace with your database name
    $username = 'root'; // Replace with your username
    $password = ''; // Replace with your password
    $charset = 'utf8mb4'; // Recommended for UTF-8 support
    
    try {
        $dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function fetchSubjects() {
    $conn = getConnection(); // Get the database connection
    
    // Prepare and execute the query to fetch all subjects
    $stmt = $conn->query("SELECT * FROM subjects"); 
    
    // Return the fetched subjects as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

function login($email, $password) {
    $validateLogin = validateLoginCredentials($email, $password);

    if(count($validateLogin) > 0){
        echo displayErrors($validateLogin);
        return;
    }


    // Get database connection
    $conn = getConnection();

    // Convert the input password to MD5
    $hashedPassword = md5($password);

    // SQL query to check if the email and hashed password match
    $query = "SELECT * FROM users WHERE email = :email AND password = :password";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    
    $stmt->execute();
    
    // Fetch the user data if found
    $user = $stmt->fetch();

    if ($user) {
        // Login successful
        // return $user;
        $_SESSION['email'] = $user['email'];
        header("Location: admin/dashboard.php");
    } else {
        // Login failed
        echo displayErrors(["Invalid email or password"]);
    }
}

function fetchSubjects() {
    $conn = getConnection();  // Get the database connection

    // SQL query to select all subjects
    $stmt = $conn->query("SELECT * FROM subjects"); 

    // Return all subjects as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function validateLoginCredentials($email, $password) {
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    
    return $errors;
}



function displayErrors($errors) {
    if (empty($errors)) return "";

    $errorHtml = '<div class="col-3 mx-auto mt-2"><div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>System Alerts</strong><ul>';

    // Make sure each error is a string
    foreach ($errors as $error) {
        // Check if $error is an array or not
        if (is_array($error)) {
            // If it's an array, convert it to a string (you could adjust this to fit your needs)
            $errorHtml .= '<li>' . implode(", ", $error) . '</li>';
        } else {
            $errorHtml .= '<li>' . htmlspecialchars($error) . '</li>';
        }
    }

    $errorHtml .= '</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>';
    

    return $errorHtml;
}

// Function to fetch subject details by subject code
function getSubjectByCode($subjectCode) {
    $conn = getConnection();  // Get the database connection

    // SQL query to select the subject with the provided subject code
    $query = "SELECT * FROM subjects WHERE subject_code = :subject_code";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':subject_code', $subjectCode);
    $stmt->execute();

    // Return the subject details as an associative array
    return $stmt->fetch();
}

function updateSubject($subjectCode, $subjectName) {
    $conn = getConnection();  // Get the database connection
    $query = "UPDATE subjects SET subject_name = :subject_name WHERE subject_code = :subject_code";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':subject_code', $subjectCode);
    $stmt->bindParam(':subject_name', $subjectName);

    // Execute the update and return the result (true or false)
    return $stmt->execute();
}

// Function to delete a subject by subject codea
function deleteSubject($subjectCode) {
    $conn = getConnection();  // Get the database connection

    // SQL query to delete the subject with the provided subject code
    $deleteQuery = "DELETE FROM subjects WHERE subject_code = :subject_code";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bindParam(':subject_code', $subjectCode);

    // Execute the deletion query and return the result
    return $deleteStmt->execute();
}

// Count the number of subjects
function countSubjects() {
    $conn = getConnection();
    $query = "SELECT COUNT(*) FROM subjects";
    $stmt = $conn->query($query);
    return $stmt->fetchColumn();
}

// Count the number of students
function countStudents() {
    $conn = getConnection();
    $query = "SELECT COUNT(*) FROM students";
    $stmt = $conn->query($query);
    return $stmt->fetchColumn();
}

function isActivePage($pageName) {
    // Get the current page's filename from the URL
    $currentPage = basename($_SERVER['PHP_SELF']);
    
    // Compare the current page with the passed $pageName and return 'font-weight-bold' if they match
    return $currentPage == $pageName ? 'font-weight-bold' : '';
}


?>