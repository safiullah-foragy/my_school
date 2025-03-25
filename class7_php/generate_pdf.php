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

// Get search criteria from POST data
$exam_criteria = $conn->real_escape_string($_POST['exam_criteria'] ?? '');
$section_criteria = $conn->real_escape_string($_POST['section_criteria'] ?? '');
$roll_criteria = $conn->real_escape_string($_POST['roll_criteria'] ?? '');

// Validate inputs
if (empty($roll_criteria) || empty($exam_criteria) || empty($section_criteria)) {
    die("Please provide all search criteria");
}

// Fetch results from the database
$sql = "SELECT * FROM results7 
        WHERE exam_type='$exam_criteria' 
        AND section='$section_criteria' 
        AND roll='$roll_criteria'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the first row for student details
    $first_row = $result->fetch_assoc();
    
    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Set font for the entire document
    $pdf->SetFont('Arial', '', 12);
    
    // School Header
    if (file_exists('school-logo.png')) {
        $pdf->Image('school-logo.png', 10, 10, 30);
    }
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Rayapur Sayed Abdul Latif Secondary School', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Class 7 Examination Results', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Exam Info
    $exam_types = [
        'half-yearly' => 'Half Yearly',
        'final' => 'Final',
        'test-exam' => 'Test Exam'
    ];
    $pdf->Cell(0, 10, 'Exam: ' . ($exam_types[$exam_criteria] ?? $exam_criteria), 0, 1);
    $pdf->Cell(0, 10, 'Section: ' . $section_criteria, 0, 1);
    $pdf->Ln(5);
    
    // Student Information
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, 'NAME:', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $first_row['name'], 0, 1);
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, 'ROLL:', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $first_row['roll'], 0, 1);
    $pdf->Ln(10);
    
    // Results Table Header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(60, 10, 'SUBJECT', 1, 0, 'C');
    $pdf->Cell(40, 10, 'OBTAINED MARKS', 1, 0, 'C');
    $pdf->Cell(40, 10, 'TOTAL MARKS', 1, 0, 'C');
    $pdf->Cell(40, 10, 'STATUS', 1, 1, 'C');
    
    // Add first result
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(60, 10, $first_row['subject'], 1);
    $pdf->Cell(40, 10, $first_row['obtained_mark'], 1, 0, 'C');
    $pdf->Cell(40, 10, $first_row['total_mark'], 1, 0, 'C');
    $pdf->Cell(40, 10, ucfirst($first_row['status']), 1, 1, 'C');
    
    // Add remaining results
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(60, 10, $row['subject'], 1);
        $pdf->Cell(40, 10, $row['obtained_mark'], 1, 0, 'C');
        $pdf->Cell(40, 10, $row['total_mark'], 1, 0, 'C');
        $pdf->Cell(40, 10, ucfirst($row['status']), 1, 1, 'C');
    }
    
    // Output PDF for download
    $pdf->Output('D', "Result_".$first_row['roll'].".pdf");
} else {
    // If no results found, return JSON response
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No results found']);
}

$conn->close();
?>