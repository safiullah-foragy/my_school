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

// Add space
$pdf->Ln(15);

// Exam Info
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Class 8 Failed Students List', 0, 1, 'C');
$pdf->Ln(5);

// Exam Details
$exam_types = [
    'half-yearly' => 'Half Yearly',
    'final' => 'Final',
    'test-exam' => 'Test Exam'
];
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Exam: ' . ($exam_types[$exam_type] ?? $exam_type), 0, 1);
$pdf->Cell(0, 10, 'Section: ' . $section, 0, 1);
$pdf->Ln(10);

// Fetch failed students
$sql = "SELECT DISTINCT r.roll, r.name 
        FROM results6 r
        WHERE r.exam_type = '$exam_type' 
        AND r.section = '$section'
        AND EXISTS (
            SELECT 1 FROM results8 
            WHERE roll = r.roll 
            AND exam_type = r.exam_type 
            AND section = r.section 
            AND status = 'failed'
        )
        ORDER BY r.roll";

$result = $conn->query($sql);

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Roll', 1, 0, 'C');
$pdf->Cell(100, 10, 'Name', 1, 0, 'C');
$pdf->Cell(50, 10, 'Failed Subjects', 1, 1, 'C');

// Table Content
$pdf->SetFont('Arial', '', 12);
$totalFailed = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Count failed subjects
        $count_sql = "SELECT COUNT(*) as failed_count 
                      FROM results8 
                      WHERE roll = '{$row['roll']}' 
                      AND exam_type = '$exam_type' 
                      AND section = '$section' 
                      AND status = 'failed'";
        $count_result = $conn->query($count_sql);
        $failed_count = $count_result->fetch_assoc()['failed_count'];
        
        $pdf->Cell(40, 10, $row['roll'], 1);
        $pdf->Cell(100, 10, '   ' . $row['name'], 1); // Added 3 spaces before name
        $pdf->Cell(50, 10, $failed_count, 1, 1, 'C');
        $totalFailed++;
    }
} else {
    $pdf->Cell(190, 10, 'No failed students found', 1, 1, 'C');
}

// Total Failed Count
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(140, 10, 'Total Failed Students:', 1);
$pdf->Cell(50, 10, $totalFailed, 1, 1, 'C');

$conn->close();

// Output PDF
$pdf->Output('D', "Failed_Students_".$section."_".$exam_type.".pdf");
?>