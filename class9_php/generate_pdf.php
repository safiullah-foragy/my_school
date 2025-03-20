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
$sql = "SELECT * FROM results9 WHERE exam_type='$exam_criteria' AND section='$section_criteria' AND roll='$roll_criteria'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the first row for student details
    $first_row = $result->fetch_assoc();
    $student_name = $first_row['name'];
    $student_roll = $first_row['roll'];

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Add school logo (top left)
    $pdf->Image('school-logo.png', 10, 10, 30); // Adjust path and dimensions as needed
    
    // School Name (centered)
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Rayapur Sayed Abdul Latif Secondary School', 0, 1, 'C');
    
    // Add space (3 lines)
    $pdf->Ln(15);

    // Exam & Section Info
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Result of Class 6", 0, 1, 'C');
    $pdf->Cell(0, 10, "Result of Section ($section_criteria)", 0, 1, 'C');
    
    // Add space (1 line)
    $pdf->Ln(5);

    // Student Name and Roll
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Name: $student_name", 0, 1);
    $pdf->Cell(0, 10, "Roll: $student_roll", 0, 1);
    
    // Add space before table
    $pdf->Ln(5);
    
    // Result Sheet Header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Subject', 1);
    $pdf->Cell(40, 10, 'Obtained Mark', 1);
    $pdf->Cell(40, 10, 'Total Mark', 1);
    $pdf->Cell(40, 10, 'Status', 1);
    $pdf->Ln();

    // Add the first row to the PDF
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40, 10, $first_row['subject'], 1);
    $pdf->Cell(40, 10, $first_row['obtained_mark'], 1);
    $pdf->Cell(40, 10, $first_row['total_mark'], 1);
    $pdf->Cell(40, 10, $first_row['status'], 1);
    $pdf->Ln();

    // Add the remaining rows to the PDF
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
