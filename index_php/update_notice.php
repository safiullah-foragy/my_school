<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'school'); // Updated database name

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO notices (content) VALUES (?)");
    $stmt->bind_param("s", $content);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>