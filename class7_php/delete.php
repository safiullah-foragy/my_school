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

// Handle record deletion
if (isset($_POST['delete-id'])) {
    $id = $conn->real_escape_string($_POST['delete-id']);
    $sql = "DELETE FROM results7 WHERE id='$id'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $conn->close();
    exit;
}

// Handle record search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roll'])) {
    $roll = $conn->real_escape_string($_POST['roll']);
    $exam_type = $conn->real_escape_string($_POST['exam-type'] ?? '');
    $section = $conn->real_escape_string($_POST['section'] ?? '');

    $sql = "SELECT * FROM results7 WHERE roll='$roll'";
    
    if (!empty($exam_type)) {
        $sql .= " AND exam_type='$exam_type'";
    }
    
    if (!empty($section)) {
        $sql .= " AND section='$section'";
    }

    $result = $conn->query($sql);

    if ($result === FALSE) {
        echo "<div class='error'>Query error: " . $conn->error . "</div>";
    } elseif ($result->num_rows > 0) {
        echo "<table class='results-table'>";
        echo "<tr>
                <th>Roll</th>
                <th>Name</th>
                <th>Subject</th>
                <th>Marks</th>
                <th>Exam Type</th>
                <th>Section</th>
                <th>Action</th>
              </tr>";
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".htmlspecialchars($row['roll'])."</td>
                    <td>".htmlspecialchars($row['name'])."</td>
                    <td>".htmlspecialchars($row['subject'])."</td>
                    <td>".htmlspecialchars($row['obtained_mark'])."/".htmlspecialchars($row['total_mark'])."</td>
                    <td>".htmlspecialchars($row['exam_type'])."</td>
                    <td>".htmlspecialchars($row['section'])."</td>
                    <td><button class='delete-btn' data-id='".htmlspecialchars($row['id'])."'>Delete</button></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='no-results'>No results found for:";
        echo "<br>Roll: ".htmlspecialchars($roll);
        if (!empty($exam_type)) echo "<br>Exam Type: ".htmlspecialchars($exam_type);
        if (!empty($section)) echo "<br>Section: ".htmlspecialchars($section);
        echo "</div>";
    }
}

$conn->close();
?>