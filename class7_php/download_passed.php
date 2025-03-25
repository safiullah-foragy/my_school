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

// Get filter criteria from POST
$exam_type = $conn->real_escape_string($_POST['exam-type'] ?? '');
$section = $conn->real_escape_string($_POST['section'] ?? '');

// Validate inputs
if (empty($exam_type) || empty($section)) {
    die("Please provide exam type and section");
}

// Fetch passed students (those with no failed subjects)
$sql = "SELECT DISTINCT r.roll, r.name 
        FROM results7 r
        WHERE r.exam_type = '$exam_type' 
        AND r.section = '$section'
        AND NOT EXISTS (
            SELECT 1 FROM results7 
            WHERE roll = r.roll 
            AND exam_type = r.exam_type 
            AND section = r.section 
            AND status = 'failed'
        )
        ORDER BY r.roll";

$result = $conn->query($sql);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// School Header
if (file_exists('school-logo.png')) {
    $pdf->Image('school-logo.png', 10, 10, 30);
}
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
$pdf->Cell(0, 10, 'Class 7 Passed Students', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Section: ' . $section, 0, 1, 'C');
$pdf->Cell(0, 10, 'Exam: ' . ($exam_types[$exam_type] ?? $exam_type), 0, 1, 'C');

// Add space before table
$pdf->Ln(15);

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Roll', 1, 0, 'C');  // Increased width
$pdf->Cell(100, 10, 'Name', 1, 0, 'C'); // Increased width
$pdf->Cell(50, 10, 'Status', 1, 1, 'C'); // Increased width

// Table Content
$pdf->SetFont('Arial', '', 12);
$totalPassed = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $row['roll'], 1);
        $pdf->Cell(100, 10, '   ' . $row['name'], 1); // Added 3 spaces before name
        $pdf->Cell(50, 10, 'Passed', 1, 1, 'C');
        $totalPassed++;
    }
} else {
    $pdf->Cell(190, 10, 'No passed students found', 1, 1, 'C');
}

// Add space before total count
$pdf->Ln(5);

// Total Passed Count
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(140, 10, 'Total Passed Students:', 1);
$pdf->Cell(50, 10, $totalPassed, 1, 1, 'C');

$conn->close();

// Output PDF
$pdf->Output('D', "Passed_Students_".$section."_".$exam_type.".pdf");
?>