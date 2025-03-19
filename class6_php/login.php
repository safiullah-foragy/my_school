<?php
session_start();

$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "class6";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get username and password from the form
$input_username = $_POST['username'];
$input_password = $_POST['password'];

// Fetch the hashed password from the database for the given username
$sql = "SELECT password_hash FROM teachers WHERE username = '$input_username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashed_password = $row['password_hash'];

    // Verify the password
    if (password_verify($input_password, $hashed_password)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $input_username;
        echo 'success';
    } else {
        echo 'failure'; // Incorrect password
    }
} else {
    echo 'failure'; // Username not found
}

$conn->close();
?>