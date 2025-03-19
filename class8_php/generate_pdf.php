<?php
error_reporting(E_ALL); // Enable error reporting
ini_set('display_errors', 1); // Display errors

require('fpdf.php'); // Include FPDF library

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class6";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search criteria from POST data
$exam_criteria = $_POST['exam_criteria'];
$section_criteria = $_POST['section_criteria'];
$roll_criteria = $_POST['roll_criteria'];

// Fetch results from the database
$sql = "SELECT * FROM results8 WHERE exam_type='$exam_criteria' AND section='$section_criteria' AND roll='$roll_criteria'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $student_name = $row['name'];
    $student_roll = $row['roll'];

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // School Name
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Rayapur Sayed Abdul Latif Secondary School', 0, 1, 'C');

    // Student Name and Roll
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Name: $student_name", 0, 1);
    $pdf->Cell(0, 10, "Roll: $student_roll", 0, 1);

    // Result Sheet Header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Subject', 1);
    $pdf->Cell(40, 10, 'Obtained Mark', 1);
    $pdf->Cell(40, 10, 'Total Mark', 1);
    $pdf->Cell(40, 10, 'Status', 1);
    $pdf->Ln();

    // Result Sheet Data
    $pdf->SetFont('Arial', '', 12);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $row['subject'], 1);
        $pdf->Cell(40, 10, $row['obtained_mark'], 1);
        $pdf->Cell(40, 10, $row['total_mark'], 1);
        $pdf->Cell(40, 10, $row['status'], 1);
        $pdf->Ln();
    }

    // Output PDF for download
    $pdf->Output('D', "Result_$student_roll.pdf");
} else {
    echo "No results found";
}

$conn->close();
?>