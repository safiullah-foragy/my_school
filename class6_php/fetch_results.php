<?php
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "class6";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get roll from the form
$roll = $_POST['roll'];

// Fetch results for the roll
$sql = "SELECT * FROM results WHERE roll='$roll'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Subject</th><th>Obtained Mark</th><th>Total Mark</th><th>Status</th><th>Action</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['subject']}</td>
                <td>{$row['obtained_mark']}</td>
                <td>{$row['total_mark']}</td>
                <td>{$row['status']}</td>
                <td><button class='delete-btn' data-id='{$row['id']}'>Delete</button></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No results found for Roll: $roll";
}

$conn->close();
?>