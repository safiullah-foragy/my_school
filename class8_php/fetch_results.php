<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "class6";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$roll = $_POST['roll'];


$sql = "SELECT * FROM results8 WHERE roll='$roll'";
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