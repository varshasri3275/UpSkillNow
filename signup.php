<?php
// Check if mysqli extension is available
if (!class_exists('mysqli')) {
    die("MySQLi extension is not enabled in your PHP installation.");
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "Varsha@1053";
$dbname = "UpSkillNow1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("DB Connection failed: " . $conn->connect_error);
    die("Could not connect to the database.");
}

// Get form data when submitted
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $raw_password = $_POST['password'];
    $course = htmlspecialchars(trim($_POST['course']));

    // Basic validation
    if ($course === "Select course") {
        echo "Please select a valid course.";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($raw_password, PASSWORD_BCRYPT);

    // Use prepared statement to insert data
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, course) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $course);

        if ($stmt->execute()) {
            echo "Registration successful! Please check your email to confirm.";
        } else {
            if (str_contains($stmt->error, 'Duplicate entry')) {
                echo "An account with this email already exists.";
            } else {
                error_log("DB Insert error: " . $stmt->error);
                echo "Something went wrong. Please try again.";
            }
        }

        $stmt->close();
    } else {
        error_log("Prepare failed: " . $conn->error);
        echo "Failed to prepare the database query.";
    }

    $conn->close();
}
?>
