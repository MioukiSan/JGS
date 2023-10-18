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
        SUM(s.total_amt) AS total_sale,
        SUM(s.product_profit) AS semi_gross_profit
        FROM sales s
        WHERE s.sale_date BETWEEN ? AND ?
        GROUP BY DATE(s.sale_date)
        ORDER BY date_order ASC";
    $resultsales = query($conn, $salesSQL, array($_POST['start_date'], $_POST['end_date']));
    $totalRevenue = 0;
    $totalWage = 0;
    $totalGrossProfit = 0;
    $totalProfit = 0;
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
$pdf->Cell(30, 8, 'DATE', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'REVENUE', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'WAGE', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'GROSS PROFIT', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'PROFIT', 1, 1, 'C', true);

$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 12);

foreach ($resultsales as $res) {
    $salary = 0;
    $date = $res['date_order'];
    $attendanceSQL = "SELECT
                        e.employee_id,
                        e.base_salary,
                        '$date' AS date,
                        CASE WHEN a.attendance_status = 'present' THEN 1 ELSE 0 END AS is_present,
                        e.base_salary * (CASE WHEN a.attendance_status = 'present' THEN 1 ELSE 0 END) AS total_base_salary
                    FROM
                        employee_management e
                    LEFT JOIN
                        attendance a
                    ON
                        e.employee_id = a.employee_id
                        AND a.date = '$date'";
    $resultAttendance = mysqli_query($conn, $attendanceSQL);

    if ($resultAttendance) {
        while ($row = mysqli_fetch_assoc($resultAttendance)) {
            $salary += $row['total_base_salary'];
        }
    }

    $filter = $res['semi_gross_profit'] - $salary;

    // Add data to the table cells
    $pdf->Cell(30, 8, $date, 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($res['total_sale'], 2), 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($salary, 2), 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($res['semi_gross_profit'], 2), 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($filter, 2), 1, 1, 'C');

    $totalRevenue += $res['total_sale'];
    $totalWage += $salary;
    $totalGrossProfit += $res['semi_gross_profit'];
    $totalProfit += $filter;
}
$pdf->SetFillColor(192, 192, 192);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 8, 'Total', 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($totalRevenue, 2), 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($totalWage, 2), 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($totalGrossProfit, 2), 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($totalProfit, 2), 1, 1, 'C', true);

    $pdf->SetFillColor(255, 255, 255); 

$pdf->Output();
?>
