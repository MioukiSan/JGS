<?php
require('../fpdf/FPDF.php');
require_once '../includes/db_conn_in_session.php';


    if(isset($_POST['transaction_code'])){
        $transaction_code = $_POST['transaction_code'];
        $invoice_sql = "SELECT 
            sales_transaction_code as transactioncode,  
            DATE(sale_date) as date_order,
            user_id, customer_name, cust_address, payment_method, tin, buss_style,
            SUM(total_amt) as total_sale
            FROM sales
            WHERE sales_transaction_code = ?
            GROUP BY sales_transaction_code, date_order";
            $result_invoice = query($conn, $invoice_sql, array($_POST['transaction_code']) );
    }
        foreach($result_invoice as $row){
            $employee_id = $row['user_id'];
            $customer_name = $row['customer_name'];  
            $customer_address = $row['cust_address'];
            $payment_method = $row['payment_method'];
            $sale_date = $row['date_order'];
            $buss_style = $row['buss_style'];
            $tin = $row['tin'];

            $get_info = "SELECT fullname, user_type FROM users WHERE user_id = '$employee_id'";
            $result_info = query($conn, $get_info);

            foreach($result_info as $res){
                $fullname = $res['fullname'];
                $usertype = $res['user_type'];
        }

        $image_path = "../images/sales_invoices.png";
        $x = 10;
        $y = 18;
        $width = 28;
        $height = 9.5;

        // $image_path = "../images/sun.png";
        // $x = 10;
        // $y = 18;
        // $width = 28;
        // $height = 9.5;

        $pdf = new FPDF('P', 'mm', array(112, 200));
        $pdf->AddPage();
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 1, 'JGS INFRASTRUCTURE BUILDER INC.', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 5, 'Purok 1, Napo, Polangui, Albay', 0, 1, 'C');
        $pdf->Cell(0, 1, 'VAT Reg. TIN: 772-128-183-000', 0, 1, 'C');
        $pdf->Ln(1);

        $pdf->Image($image_path, $x, $y, $width, $height);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(69, 8, '', 0);
        $pdf->SetTextColor(255, 0, 0);
        $pdf->SetFont('Courier', 'B', 10);
        $pdf->Cell(5, 8, $transaction_code);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10);


        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(55, 5, 'Sold to: ' . $customer_name, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(2, 5, 'Date: ' . '   ' . $sale_date);
        $pdf->Ln(4);


        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(55, 5, 'TIN: ' . $tin, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(2, 5, 'Payment Method: ' . $payment_method, 0);
        $pdf->Ln(4);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(55, 5, 'Address: ' . $customer_address, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(2, 5, 'Business Style: ' . $buss_style, 0);
        $pdf->Ln(7);


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(9, 5, 'QTY', 1);
        $pdf->Cell(10, 5, 'UNIT', 1);
        $pdf->Cell(32, 5, 'ARTICLES', 1, 0, 'C');
        $pdf->Cell(21, 5, 'UNIT PRICE', 1, 0, 'C');
        $pdf->Cell(21, 5, 'AMOUNT', 1, 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $total = 0;

        $item_sql = "SELECT p.item_name, s.product_qty, s.total_amt, p.acronym, s.product_id, p.retail_price, p.product_unit
            FROM sales s
            JOIN products p ON s.product_id = p.product_id
            WHERE s.sales_transaction_code = ?";
            $result_item = query($conn, $item_sql, array($_POST['transaction_code']) );

            foreach ($result_item as $item) {
              $qty = $item['product_qty'];
              $unit = $item['product_unit'];
              $item_name = $item['item_name'];
              $acronym = $item['acronym'];
              $retail_price = $item['retail_price'];
              $total_amt = $item['total_amt'];
            
            $pdf->Cell(9, 5, $qty, 1);
            $pdf->Cell(10, 5, $unit, 1);
            $pdf->Cell(32, 5, $acronym, 1);
            $pdf->Cell(21   , 5, 'Php' . number_format($retail_price, 2), 1, 0, 'R');
            $pdf->Cell(21   , 5, 'Php' . number_format($total_amt, 2), 1, 0, 'R');
            $pdf->Ln();
            
            $total += $total_amt;

        }
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(9, 5, '', 1);
        $pdf->Cell(10, 5, '', 1);
        $pdf->Cell(32, 5, '', 1);
        $pdf->Cell(21, 5, '', 1);
        $pdf->Cell(21, 5, '', 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(9, 5, '', 1);
        $pdf->Cell(10, 5, '', 1);
        $pdf->Cell(32, 5, '', 1);
        $pdf->Cell(21, 5, '', 1);
        $pdf->Cell(21, 5, '', 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(9, 5, '', 1);
        $pdf->Cell(10, 5, '', 1);
        $pdf->Cell(32, 5, '', 1);
        $pdf->Cell(21, 5, '', 1);
        $pdf->Cell(21, 5, '', 1);
        $pdf->Ln();
        //VAT section
        $pdf->SetFont('Arial', 'B',5);
        $pdf->Cell(23, 5, 'VATable Sales', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Cell(24, 5, 'Total Sales (VAT Inclusive)', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(23, 5, 'VAR-Exempt Sales', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Cell(24, 5, 'Less VAT', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(23, 5, 'Zero Rated Sales', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Cell(24, 5, 'Amount Net of VAT', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(23, 5, 'VAT Amount', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Cell(24, 5, 'Less: SC/PWD Discount', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(23, 5, 'Withholding Tax', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Cell(24, 5, 'Amount Due', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Cell(24, 5, 'Add VAT', 1);
        $pdf->Cell(23, 5, '', 1);
        $pdf->Ln();
        // Total
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(72, 5, '                                  Total Amount Due', 1);
        $pdf->Cell(21, 5, 'Php' . number_format($total, 2), 1, 0, 'R');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', '' , 5);
        $pdf->Cell(0, 0, '25 Bklts. (50x3) 1251-2500');
        $pdf->Ln();

        $pdf->SetFont('Arial', '' , 5);
        $pdf->Cell(0, 4, 'BIR Authority: to Print No. 067AU20220000005303', 0);
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '' , 5);
        $pdf->Cell(0, 0, 'Date Issued: 10-05-2022');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 5, 'TRIPLE J Printing Press, Leg. City                             ________________________________');
        $pdf->Cell(-8, 5, '' . $fullname . '-' . $usertype  , 0, 1, 'R');
        $pdf->Cell(0, 0, 'TIN: 144-888-113-000 T (052) 201-9722                          Cashier/Authorized Representative');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '' , 5);
        $pdf->Cell(0, 0, 'Printers Apcreditation No. 067MP20190000000003 Date Issued: 01-23-2019
        Expiry Date: 01-23-2024');
        $pdf->Ln();
        //lines
        $pdf->Line(74, 31.6, 103, 31.6);
        $pdf->Line(21, 31.6, 65, 31.6);

        $pdf->Line(17, 35.6, 65, 35.6);
        $pdf->Line(88, 35.6, 103, 35.6);

        $pdf->Line(22, 39.6, 65, 39.6);
        $pdf->Line(86, 39.6, 103, 39.6);
        // Output the PDF
        $pdf->Output();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="images/jgs.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>
<body>
    
</body>
</html>