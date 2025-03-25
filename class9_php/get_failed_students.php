<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    die('Unauthorized access');
}

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

// Fetch students who failed at least one subject
$sql = "SELECT DISTINCT r.roll, r.name 
        FROM results9 r
        WHERE r.exam_type = '$exam_type' 
        AND r.section = '$section'
        AND EXISTS (
            SELECT 1 FROM results9 
            WHERE roll = r.roll 
            AND exam_type = r.exam_type 
            AND section = r.section 
            AND status = 'failed'
        )
        ORDER BY r.roll";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='failed-students-table'>
            <thead>
                <tr>
                    <th>Roll</th>
                    <th>Name</th>
                    <th>Section</th>
                    <th>Exam Type</th>
                    <th>Failed Subjects</th>
                </tr>
            </thead>
            <tbody>";
    
    while ($row = $result->fetch_assoc()) {
        // Count failed subjects for each student
        $count_sql = "SELECT COUNT(*) as failed_count 
                      FROM results9 
                      WHERE roll = '{$row['roll']}' 
                      AND exam_type = '$exam_type' 
                      AND section = '$section' 
                      AND status = 'failed'";
        $count_result = $conn->query($count_sql);
        $failed_count = $count_result->fetch_assoc()['failed_count'];
        
        echo "<tr>
                <td>" . htmlspecialchars($row['roll']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
                <td>" . htmlspecialchars($section) . "</td>
                <td>" . htmlspecialchars(ucfirst(str_replace('-', ' ', $exam_type))) . "</td>
                <td>" . htmlspecialchars($failed_count) . "</td>
              </tr>";
    }
    
    echo "</tbody></table>";
} else {
    echo "<div class='no-results'>No failed students found for " . 
         htmlspecialchars(ucfirst(str_replace('-', ' ', $exam_type))) . 
         " exam in section " . htmlspecialchars($section) . ".</div>";
}

$conn->close();
?>