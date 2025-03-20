<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class6";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roll = $_POST['roll'] ?? null;
$examCriteria = $_POST['exam-criteria'] ?? null;
$sectionCriteria = $_POST['section-criteria'] ?? null;

if (empty($roll) || empty($examCriteria) || empty($sectionCriteria)) {
    die("Error: Missing required input data.");
}

$sql = "DELETE FROM results8 WHERE roll = ? AND exam_type = ? AND section = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("sss", $roll, $examCriteria, $sectionCriteria);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Record deleted successfully";
    } else {
        echo "No record found with the provided criteria.";
    }
} else {
    echo "Error deleting record: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>