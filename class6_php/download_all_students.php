<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    die('Unauthorized access');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require('fpdf.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class6";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter criteria
$exam_type = $conn->real_escape_string($_POST['exam-type'] ?? '');
$section = $conn->real_escape_string($_POST['section'] ?? '');

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Add school logo (top left)
$pdf->Image('school-logo.png', 10, 10, 30);

// School Header (centered)
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Rayapur Sayed Abdul Latif Secondary School', 0, 1, 'C');

// Add space (3 lines)
$pdf->Ln(15);

// Exam Info
$exam_types = [
    'half-yearly' => 'Half Yearly',
    'final' => 'Final',
    'test-exam' => 'Test Exam'
];
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Class 6 All Students List', 0, 1, 'C');
$pdf->Ln(5); // Add small space after title

// Exam Details with proper spacing
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Exam: ' . ($exam_types[$exam_type] ?? $exam_type), 0, 1);
$pdf->Cell(0, 10, 'Section: ' . $section, 0, 1);
$pdf->Ln(10); // Add space before table

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Roll', 1, 0, 'C');
$pdf->Cell(160, 10, 'Name', 1, 1, 'C'); // Wider name column since we removed two columns

// Fetch and display all students - sorted numerically by roll
$sql = "SELECT DISTINCT roll, name 
        FROM results6 
        WHERE exam_type = '$exam_type' 
        AND section = '$section'
        ORDER BY CAST(roll AS SIGNED) ASC"; // Cast to integer for proper numeric sorting
$result = $conn->query($sql);

$pdf->SetFont('Arial', '', 12);
$totalStudents = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(30, 10, $row['roll'], 1);
        $pdf->Cell(160, 10, '   ' . $row['name'], 1, 1); // 3 spaces before name
        $totalStudents++;
    }
} else {
    $pdf->Cell(190, 10, 'No students found', 1, 1, 'C');
}

// Add space before total count
$pdf->Ln(5);

// Total Students Count
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(130, 10, 'Total Students:', 1);
$pdf->Cell(60, 10, $totalStudents, 1, 1, 'C');

$conn->close();

// Output PDF
$pdf->Output('D', "All_Students_".$section."_".$exam_type.".pdf");
?>