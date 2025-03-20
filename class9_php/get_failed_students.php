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
$sql = "SELECT DISTINCT roll, name FROM results9 WHERE status = 'failed'";
if ($section && $examType) {
    $sql .= " AND section = '$section' AND exam_type = '$examType'";
} elseif ($section) {
    $sql .= " AND section = '$section'";
} elseif ($examType) {
    $sql .= " AND exam_type = '$examType'";
}

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
    echo "No failed students found for the selected criteria.";
}

$conn->close();
?>