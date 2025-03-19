<?php
session_start();

// Check if the teacher is logged in
if (!isset($_SESSION['loggedin'])) {
    die('Unauthorized access');
}

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "class6";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$roll = $_POST['roll'];
$name = $_POST['name'];
$subject = $_POST['subject'];
$obtained_mark = $_POST['obtained-mark'];
$total_mark = $_POST['total-mark'];
$exam_type = $_POST['exam-type'];
$section = $_POST['section'];
$status = $_POST['status'];


$sql = "INSERT INTO results7 (roll, name, subject, obtained_mark, total_mark, exam_type, section, status)
        VALUES ('$roll', '$name', '$subject', '$obtained_mark', '$total_mark', '$exam_type', '$section', '$status')";

if ($conn->query($sql) === TRUE) {
    echo "Result inserted successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>