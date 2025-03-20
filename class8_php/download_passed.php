<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include FPDF library
require('fpdf.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class8"; // Updated to class8 as per your requirement

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get exam criteria and section criteria from the POST request
$examCriteria = $_POST['exam-criteria'] ?? ''; // Default to empty if not provided
$sectionCriteria = $_POST['section-criteria'] ?? ''; // Default to empty if not provided

// Fetch passed students for the selected exam and section
$sql = "SELECT DISTINCT roll, name 
        FROM results8 
        WHERE status = 'passed' 
        AND exam_criteria = ? 
        AND section_criteria = ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

// Bind the parameters
$stmt->bind_param("ss", $examCriteria, $sectionCriteria);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Add school logo (top left)
$pdf->Image('school-logo.png', 10, 10, 30); // Adjust path and dimensions as needed

// Add school name (top center)
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Rayapur Sayed Abdul Latif Secondary School', 0, 1, 'C');

// Add space (3 lines)
$pdf->Ln(15); // 15 units of space (approximately 3 lines)

// Add "Result of Class 8" heading
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Result of Class 8', 0, 1, 'C');

// Add "Result of Section (A/B)" heading
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Result of Section ' . $sectionCriteria, 0, 1, 'C');

// Add "Exam Type" heading
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Exam Type: ' . ucfirst($examCriteria), 0, 1, 'C');

// Add space (1 line)
$pdf->Ln(5);

// Set font for the table
$pdf->SetFont('Arial', '', 12);

// Define table width and position
$tableWidth = 100; // Total width of the table
$leftMargin = ($pdf->GetPageWidth() - $tableWidth) / 2; // Center the table

// Create table header
$pdf->SetX($leftMargin); // Set X position to center the table
$pdf->Cell(40, 10, 'Roll', 1);
$pdf->Cell(60, 10, 'Name', 1);
$pdf->Ln();

// Add table rows and count the number of students
$totalStudents = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->SetX($leftMargin); // Set X position to center the table
        $pdf->Cell(40, 10, $row['roll'], 1);
        $pdf->Cell(60, 10, $row['name'], 1);
        $pdf->Ln();
        $totalStudents++; // Increment the student count
    }
} else {
    $pdf->SetX($leftMargin); // Set X position to center the table
    $pdf->Cell(100, 10, 'No passed students found.', 1, 1, 'C');
}

// Add "Total Passed" row
$pdf->SetX($leftMargin); // Set X position to center the table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Total Passed', 1); // "Total Passed" in the Roll column
$pdf->Cell(60, 10, $totalStudents, 1); // Total number in the Name column
$pdf->Ln();

// Close the statement and connection
$stmt->close();
$conn->close();

// Output PDF
$pdf->Output('D', 'passed_students.pdf');
?>