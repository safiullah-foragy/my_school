<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class6";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search criteria
$exam_criteria = $_POST['exam-criteria'];
$section_criteria = $_POST['section-criteria'];
$roll_criteria = $_POST['roll-criteria'];

// Fetch results
$sql = "SELECT * FROM results9 WHERE exam_type='$exam_criteria' AND section='$section_criteria' AND roll='$roll_criteria'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the first row to get the name (assuming name is the same for all rows of the same roll)
    $row = $result->fetch_assoc();
    $student_name = $row['name'];

    // Display name and roll
    echo "<h3>Name: $student_name</h3>";
    echo "<h3>Roll: $roll_criteria</h3>";

    // Display results in a table
    echo "<table>";
    echo "<tr><th>Subject</th><th>Obtained Mark</th><th>Total Mark</th><th>Status</th></tr>";

    // Output the first row
    echo "<tr>
            <td>{$row['subject']}</td>
            <td>{$row['obtained_mark']}</td>
            <td>{$row['total_mark']}</td>
            <td>{$row['status']}</td>
          </tr>";

    // Output the remaining rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['subject']}</td>
                <td>{$row['obtained_mark']}</td>
                <td>{$row['total_mark']}</td>
                <td>{$row['status']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No results found";
}

$conn->close();
?>