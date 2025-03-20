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

// Fetch students who passed all subjects
$sql = "SELECT roll, name 
        FROM results7 
        GROUP BY roll, name 
        HAVING SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) = 0";

$result = $conn->query($sql);

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
    echo "No passed students found.";
}

$conn->close();
?>
