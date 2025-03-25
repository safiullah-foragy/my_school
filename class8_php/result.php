<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    die('Unauthorized access');
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class6";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search criteria
$roll = $conn->real_escape_string($_POST['roll-criteria'] ?? '');
$exam_type = $conn->real_escape_string($_POST['exam-criteria'] ?? '');
$section = $conn->real_escape_string($_POST['section-criteria'] ?? '');

// Validate inputs
if (empty($roll) || empty($exam_type) || empty($section)) {
    die("Please provide all search criteria");
}

// Query database
$sql = "SELECT * FROM results8
        WHERE roll='$roll' 
        AND exam_type='$exam_type' 
        AND section='$section'";

$result = $conn->query($sql);

if ($result === false) {
    echo "<div class='error'>Database error: " . $conn->error . "</div>";
} elseif ($result->num_rows > 0) {
    // Get first row for student info
    $first_row = $result->fetch_assoc();
    
    // Display student header with exact format requested
    echo "<div class='student-header'>";
    echo "<div class='student-info'><span class='info-label'>NAME:</span> " . htmlspecialchars($first_row['name']) . "</div>";
    echo "<div class='student-info'><span class='info-label'>ROLL:</span> " . htmlspecialchars($first_row['roll']) . "</div>";
    echo "</div>";
    
    // Display results table
    echo "<table class='result-table'>";
    echo "<thead>
            <tr>
                <th>SUBJECT</th>
                <th>OBTAINED MARKS</th>
                <th>TOTAL MARKS</th>
                <th>STATUS</th>
            </tr>
          </thead>";
    echo "<tbody>";
    
    // Display first result
    echo "<tr>
            <td>" . htmlspecialchars($first_row['subject']) . "</td>
            <td>" . htmlspecialchars($first_row['obtained_mark']) . "</td>
            <td>" . htmlspecialchars($first_row['total_mark']) . "</td>
            <td>" . htmlspecialchars(ucfirst($first_row['status'])) . "</td>
          </tr>";
    
    // Display remaining results if any
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['subject']) . "</td>
                <td>" . htmlspecialchars($row['obtained_mark']) . "</td>
                <td>" . htmlspecialchars($row['total_mark']) . "</td>
                <td>" . htmlspecialchars(ucfirst($row['status'])) . "</td>
              </tr>";
    }
    
    echo "</tbody></table>";
} else {
    echo "<div class='no-results'>No results found for the specified criteria.</div>";
}

$conn->close();
?>