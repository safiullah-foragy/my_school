<?php
session_start();

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "class6";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$input_username = $_POST['username'];
$input_password = $_POST['password'];


$sql = "SELECT password_hash FROM teachers8 WHERE username = '$input_username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashed_password = $row['password_hash'];

   
    if (password_verify($input_password, $hashed_password)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $input_username;
        echo 'success';
    } else {
        echo 'failure';
    }
} else {
    echo 'failure';  
}

$conn->close();
?>