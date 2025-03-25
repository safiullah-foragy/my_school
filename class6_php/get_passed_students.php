<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class6";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter criteria
$exam_type = $conn->real_escape_string($_POST['exam-type'] ?? '');
$section = $conn->real_escape_string($_POST['section'] ?? '');

// Fetch students who passed all subjects in the selected exam and section
$sql = "SELECT roll, name 
        FROM results6 
        WHERE exam_type = '$exam_type' AND section = '$section'
        GROUP BY roll, name 
        HAVING SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) = 0
        ORDER BY roll";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='passed-students-table'>
            <thead>
                <tr>
                    <th>Roll</th>
                    <th>Name</th>
                    <th>Section</th>
                    <th>Exam Type</th>
                </tr>
            </thead>
            <tbody>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['roll']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($section) . "</td>
                <td>" . htmlspecialchars(ucfirst(str_replace('-', ' ', $exam_type))) . "</td>
              </tr>";
    }
    
    echo "</tbody></table>";
} else {
    echo "<div class='no-results'>No passed students found for " . 
         htmlspecialchars(ucfirst(str_replace('-', ' ', $exam_type))) . 
         " exam in section " . htmlspecialchars($section) . ".</div>";
}

$conn->close();
?>