<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'school'); // Updated database name

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = 'admin'; // Hardcoded username
    $input_password = $_POST['admin-password'];

    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($input_password, $hashed_password)) {
        $_SESSION['loggedin'] = true; // Start a session
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>