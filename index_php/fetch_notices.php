<?php
$conn = new mysqli('localhost', 'root', '', 'school'); // Updated database name

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT content FROM notices ORDER BY created_at DESC LIMIT 1");
$notice = $result->fetch_assoc();
echo $notice ? $notice['content'] : "No notices available.";
?>