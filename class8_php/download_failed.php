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
$dbname = "class6";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch failed students
$sql = "SELECT DISTINCT roll, name FROM results8 WHERE status = 'failed'";
$result = $conn->query($sql);

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

// Add "Result of Class 6, 7, 8" heading
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Result of Class 6', 0, 1, 'C'); // Change "Class 7" to the appropriate class

// Add "Result of Section (A/B)" heading
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Result of Section (A/B)', 0, 1, 'C');

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
    $pdf->Cell(100, 10, 'No failed students found.', 1, 1, 'C');
}

// Add "Total Failed" row
$pdf->SetX($leftMargin); // Set X position to center the table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Total Failed', 1); // "Total Failed" in the Roll column
$pdf->Cell(60, 10, $totalStudents, 1); // Total number in the Name column
$pdf->Ln();

$conn->close();

// Output PDF
$pdf->Output('D', 'failed_students.pdf');
?>