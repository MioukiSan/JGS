<?php
require('../fpdf/FPDF.php');
require_once '../includes/db_conn_in_session.php';

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

if (isset($_POST['viewrep'])) {
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    $salesSQL = "SELECT
                DATE(s.sale_date) as date_order,
                SUM(s.total_amt) AS total_sale
              FROM sales s
              WHERE s.sale_date BETWEEN ? AND ?
              GROUP BY DATE(s.sale_date)
              ORDER BY date_order ASC";
    $resultsales = query($conn, $salesSQL, array($_POST['start_date'], $_POST['end_date']));
    $totalRevenue = 0;
}

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 1, 'JGS INFRASTRUCTURE BUILDER INC.', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Purok 1, Napo, Polangui, Albay', 0, 1, 'C');
$pdf->Cell(0, 1, 'VAT Reg. TIN: 772-128-183-000', 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, 'SALES REPORT', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, $start . ' - ' . $end, 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(52, 162, 192);
$pdf->Cell(95, 8, 'DATE', 1, 0, 'C', true);
$pdf->Cell(95, 8, 'REVENUE', 1, 1, 'C', true);

$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 12);

foreach ($resultsales as $res) {
    $date = $res['date_order'];
    $pdf->Cell(95, 8, $date, 1, 0, 'C');
    $pdf->Cell(95, 8, number_format($res['total_sale'], 2), 1, 1, 'C');

    $totalRevenue += $res['total_sale'];
}

$pdf->SetFillColor(192, 192, 192);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(95, 8, 'Total', 1, 0, 'C', true);
$pdf->Cell(95, 8, number_format($totalRevenue, 2), 1, 1, 'C', true);

$pdf->SetFillColor(255, 255, 255);

session_start();
$user_id = $_SESSION['user_id'];
$fullnameSQL = "SELECT fullname, user_type FROM users WHERE user_id = '$user_id'";
$fullnameResult = mysqli_query($conn, $fullnameSQL);
$fullnameRow = mysqli_fetch_assoc($fullnameResult);
$fullname = $fullnameRow['fullname'];
$type = $fullnameRow['user_type'];

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Prepared by: ' . $fullname .' - '. $type, 0, 0, 'R');
$pdf->Output();
?>
