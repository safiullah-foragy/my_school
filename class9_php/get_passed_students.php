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

// Check if section and examType are provided in the request
$section = isset($_POST['section']) ? $_POST['section'] : null;
$examType = isset($_POST['examType']) ? $_POST['examType'] : null;

// Build the SQL query based on the provided filters
$sql = "SELECT roll, name 
        FROM results9 
        WHERE 1=1"; // Start with a condition that is always true

// Add filters for section and examType if provided
if ($section) {
    $sql .= " AND section = '$section'";
}
if ($examType) {
    $sql .= " AND exam_type = '$examType'";
}

// Group by roll and name, and filter students who passed all subjects
$sql .= " GROUP BY roll, name 
          HAVING SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) = 0";

// Execute the query
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

if ($result->num_rows > 0) {
    // Display the results in a table
    echo "<table border='1'>
            <tr>
                <th>Roll</th>
                <th>Name</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['roll']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No passed students found for the selected criteria.";
}

$conn->close();
?>