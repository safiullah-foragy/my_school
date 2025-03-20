<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class8"; // Updated to class8 as per your requirement

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get exam criteria and section criteria from the POST request
$examCriteria = $_POST['exam-criteria'] ?? ''; // Default to empty if not provided
$sectionCriteria = $_POST['section-criteria'] ?? ''; // Default to empty if not provided

// Fetch students who failed at least one subject for the selected exam and section
$sql = "SELECT DISTINCT roll, name 
        FROM results8 
        WHERE status = 'failed' 
        AND exam_criteria = ? 
        AND section_criteria = ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

// Bind the parameters
$stmt->bind_param("ss", $examCriteria, $sectionCriteria);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Roll</th>
                <th>Name</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['roll'] . "</td>
                <td>" . $row['name'] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No failed students found for the selected criteria.";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>