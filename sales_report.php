<?php 
    require_once './includes/db_conn.php';
    require_once './includes/restriction.php';
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    
        $check_user_query = "SELECT user_type FROM users WHERE user_id = ?";
        $result = query($conn, $check_user_query, [$user_id]);
    
        if (count($result) > 0) {
            $user_type = $result[0]['user_type'];
            if ($user_type === "cashier") {
                if (isset($_SESSION['last_visited_url'])) {
                    header("Location: " . $_SESSION['last_visited_url']);
                } else {
                    header("Location: pos.php");
                }
                exit();
            }
        }
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALES REPORT</title>
    <?php include_once './includes/head.php'; ?>
</head>
<body class="bg-light pad">
    <?php include_once './includes/side_navbar.php'; ?>
    <div class="container">
        <div class="row pt-4">
            <form action="" method="POST">
              <div class="input-group mb-3 shadow-sm">
                  <span class="input-group-text">Between </span>
                  <input type="date" name="start_date" class="form-control">
                  <span class="input-group-text"> and </span>
                  <input type="date" name="end_date" class="form-control">
                  <input type="submit" name="filter_range" value="filter" class="btn btn-primary">
              </div>
            </form>
            <form action="" method="POST">
                <div class="input-group mb-3 shadow-sm">
                    <span class="input-group-text">For These Date</span>
                    <input type="date" name="this_date" class="form-control">
                    <input type="submit" name="filter_date" value="Filter" class="btn btn-primary">
                </div>
            </form>
            <div class="col-12">
                <div class="table text-center bg-white shadow-sm">
                    <table class="table table-borderless">
                        <thead class="table-info">
                            <tr>
                                <td>
                                    <div class="dropdown dropdown-sales">
                                        <button class="btn dropdown-toggle sales-dropdown" type="button" data-bs-toggle="collapse" aria-expanded="false">
                                            Date
                                        </button>
                                        <ul class="dropdown-menu">
                                        <form action="" method="POST">
                                            <li><button type="submit" name="daily" class="dropdown-item">Daily</button></li>
                                            <li><button type="submit" name="weekly" class="dropdown-item">Weekly</button></li>
                                            <li><button type="submit" name="monthly" class="dropdown-item">Monthly</button></li>
                                            <li><button type="submit" name="annual" class="dropdown-item">Annual</button></li>
                                        </form>
                                        </ul>
                                    </div>
                                </td>
                                <td>Sales Revenue</td>
                                <td>Wages</td>
                                <td>Gross Profit</td>
                                <td>Profit</td>
                            </tr>
                        </thead>
                        <?php
                            $profit = 0;
                            
                            if (isset($_POST['filter_date'])) {
                                $transaction_id = array();

                                $salesSQL = "SELECT
                                    
                                    DATE(s.sale_date) as date_order,
                                    SUM(s.total_amt) AS total_sale,
                                    SUM(s.product_profit) AS semi_gross_profit
                                    FROM sales s
                                    WHERE DATE(s.sale_date) = ? 
                                    GROUP BY date_order
                                    ORDER BY date_order DESC";
                                    $resultsales = query($conn, $salesSQL, array($_POST['this_date']));
                            }
                            
                            elseif(isset($_POST['filter_range'])) {                            
                                $salesSQL = "SELECT
                                    DATE(s.sale_date) as date_order,
                                    SUM(s.total_amt) AS total_sale,
                                    SUM(s.product_profit) AS semi_gross_profit
                                    FROM sales s
                                    WHERE s.sale_date BETWEEN ? AND ?
                                    GROUP BY DATE(s.sale_date)
                                    ORDER BY date_order ASC";
                                    $resultsales = query($conn, $salesSQL, array($_POST['start_date'], $_POST['end_date']));
                            }
                             elseif(isset($_POST['daily'])){
                                $salesSQL = "SELECT
                                    s.sales_transaction_code as transactioncode,
                                    COUNT(DISTINCT DATE(s.sale_date)) AS salexdate,
                                    DATE(s.sale_date) as date_order,
                                    SUM(s.total_amt) as total_sale,
                                    SUM(s.product_profit) as semi_gross_profit,
                                    (SELECT SUM(base_salary) FROM employee_management) as total_base_salary
                                    FROM sales s
                                    GROUP BY date_order
                                    ORDER BY date_order DESC";
                                    $resultsales = query($conn, $salesSQL);

                            }elseif (isset($_POST['weekly'])){
                                $salesSQL = "SELECT
                                    WEEK(s.sale_date) as week_number,
                                    MIN(DATE(s.sale_date)) as week_start_date,
                                    MAX(DATE(s.sale_date)) as week_end_date,
                                    COUNT(DISTINCT DATE(s.sale_date)) as salexdate,
                                    SUM(s.total_amt) as total_sale,
                                    SUM(s.product_profit) as semi_gross_profit,
                                    (SELECT SUM(base_salary) FROM employee_management) as total_base_salary
                                    FROM sales s
                                    GROUP BY week_number
                                    ORDER BY week_number DESC";

                                    $resultsales = query($conn, $salesSQL);
                                    
                            }elseif (isset($_POST['monthly'])){
                                $salesSQL = "SELECT
                                    DATE_FORMAT(s.sale_date, '%Y-%m') AS month_year,
                                    DATE_FORMAT(MIN(s.sale_date), '%Y-%m-%d') AS month_start,
                                    DATE_FORMAT(LAST_DAY(s.sale_date), '%Y-%m-%d') AS month_end,
                                    COUNT(DISTINCT DATE(s.sale_date)) AS salexdate,
                                    SUM(s.total_amt) AS total_sale,
                                    SUM(s.product_profit) AS semi_gross_profit,
                                    (SELECT SUM(base_salary) FROM employee_management) AS total_base_salary
                                    FROM sales s
                                    GROUP BY month_year
                                    ORDER BY month_year DESC";
                                    $resultsales = query($conn, $salesSQL);
                            }elseif (isset($_POST['annual'])){
                                $currentYear = date('Y');

                                $startOfYear = "{$currentYear}-01-01";
                                $endOfYear = "{$currentYear}-12-31";

                                $salesSQL = "SELECT
                                    YEAR(s.sale_date) AS year,
                                    COUNT(DISTINCT DATE(s.sale_date)) AS salexdate,
                                    SUM(s.total_amt) AS total_sale,
                                    SUM(s.product_profit) AS semi_gross_profit,
                                    (SELECT SUM(base_salary) FROM employee_management) AS total_base_salary,
                                    CONCAT(YEAR(s.sale_date), '-01-01') AS year_start,
                                    CONCAT(YEAR(s.sale_date), '-12-31') AS year_end
                                    FROM sales s
                                    GROUP BY year
                                    ORDER BY year DESC";

                                    $resultsales = query($conn, $salesSQL);
                            }else{
                                $salesSQL = "SELECT
                                    s.sales_transaction_code as transactioncode,
                                    COUNT(DISTINCT DATE(s.sale_date)) AS salexdate,
                                    DATE(s.sale_date) as date_order,
                                    SUM(s.total_amt) as total_sale,
                                    SUM(s.product_profit) as semi_gross_profit,
                                    (SELECT SUM(base_salary) FROM employee_management) as total_base_salary
                                    FROM sales s
                                    GROUP BY date_order 
                                    ORDER BY date_order DESC";
                                    $resultsales = query($conn, $salesSQL);
                            }

                                foreach($resultsales as $res) {
                                    $salary = 0;
                                    
                                    if (isset($_POST['weekly'])) {
                                        $week_start = $res['week_start_date'];
                                        $week_end = $res['week_end_date'];
                                        
                                        // Fetch all transaction IDs between the specified date range
                                        $transactionIds = array();
                                        $transactionIdsSQL = "SELECT DISTINCT s.transaction_id
                                            FROM sales s
                                            WHERE s.sale_date BETWEEN '$week_start' AND '$week_end'";
                                        $resultTransactionIds = mysqli_query($conn, $transactionIdsSQL);
                                        if ($resultTransactionIds) {
                                            while ($row = mysqli_fetch_assoc($resultTransactionIds)) {
                                                $transactionIds[] = $row['transaction_id'];
                                            }
                                        }
                                    
                                        // Use the transaction IDs to calculate salary
                                        $attendanceSQL = "SELECT
                                            DATE(s.sale_date) AS date,
                                            SUM(e.base_salary * (CASE WHEN a.attendance_status = 'present' THEN 1 ELSE 0 END)) AS total_base_salary
                                        FROM
                                            employee_management e
                                            LEFT JOIN attendance a ON e.employee_id = a.employee_id AND a.date BETWEEN '$week_start' AND '$week_end'
                                            LEFT JOIN sales s ON e.employee_id = s.employee_id AND DATE(s.sale_date) BETWEEN '$week_start' AND '$week_end'
                                        WHERE
                                            s.sales_transaction_code IN ('" . implode("','", $transactionIds) . "')
                                        GROUP BY
                                            DATE(s.sale_date)
                                        ORDER BY
                                            DATE(s.sale_date) DESC";
                                        $resultAttendance = mysqli_query($conn, $attendanceSQL);
                                        if ($resultAttendance) {
                                            while ($row = mysqli_fetch_assoc($resultAttendance)) {
                                                $salary += $row['total_base_salary'];
                                            }
                                        }
                                    }
                                    elseif(isset($_POST['monthly'])){
                                        $month_start = $res['month_start'];
                                        $month_end = $res['month_end'];
                                        $attendanceSQL = "SELECT
                                            DATE(s.sale_date) AS date,
                                            SUM(e.base_salary * (CASE WHEN a.attendance_status = 'present' THEN 1 ELSE 0 END)) AS total_base_salary
                                        FROM
                                            employee_management e
                                            LEFT JOIN attendance a ON e.employee_id = a.employee_id AND a.date BETWEEN '$month_start' AND '$month_end'
                                            LEFT JOIN sales s ON e.employee_id = s.employee_id AND DATE(s.sale_date) BETWEEN '$month_start' AND '$month_end'
                                        GROUP BY
                                            DATE(s.sale_date)
                                        ORDER BY
                                            DATE(s.sale_date) DESC";
                                        $resultAttendance = mysqli_query($conn, $attendanceSQL);
                                        if ($resultAttendance) {
                                            while ($row = mysqli_fetch_assoc($resultAttendance)) {
                                                $salary += $row['total_base_salary'];
                                                }
                                            }
                                    }elseif(isset($_POST['annual'])){
                                        $year_start = $res['year_start'];
                                        $year_end = $res['year_end'];
                                        $attendanceSQL = "SELECT
                                            DATE(s.sale_date) AS date,
                                            SUM(e.base_salary * (CASE WHEN a.attendance_status = 'present' THEN 1 ELSE 0 END)) AS total_base_salary
                                        FROM
                                            employee_management e
                                            LEFT JOIN attendance a ON e.employee_id = a.employee_id AND a.date BETWEEN '$year_start' AND '$year_end'
                                            LEFT JOIN sales s ON e.employee_id = s.employee_id AND DATE(s.sale_date) BETWEEN '$year_start' AND '$year_end'
                                        GROUP BY
                                            DATE(s.sale_date)
                                        ORDER BY
                                            DATE(s.sale_date) DESC";
                                        $resultAttendance = mysqli_query($conn, $attendanceSQL);
                                        if ($resultAttendance) {
                                            while ($row = mysqli_fetch_assoc($resultAttendance)) {
                                                $salary += $row['total_base_salary'];
                                                }
                                            }
                                    }else{
                                    $date =$res['date_order'];
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
                                    }

                                        $filter = $res['semi_gross_profit'] - $salary;
                            ?>
                                <tr>
                                    <td>
                                        <?php 
                                            if(isset($POST['daily'])){echo $res['date_order'];
                                            }elseif(isset($_POST['weekly'])){ echo $res['week_start_date'] . "-" . $res['week_end_date']; 
                                            }elseif(isset($_POST['monthly'])){ echo $res['month_start'] . "-" . $res['month_end']; 
                                            }elseif(isset($_POST['annual'])){ echo $res['year_start'] . "-" . $res['year_end']; 
                                            }elseif(isset($_POST['filter_range'])){ echo $res['date_order'];
                                            }elseif(isset($_POST['filter_date'])){ echo $_POST['this_date']; 
                                            }else{ echo $res['date_order']; } ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn" data-bs-toggle="collapse" data-bs-target="#"> <?php echo CURRENCY . number_format($res['total_sale'], 2); ?></button>
                                        <!-- <?php var_dump($transactionIds) ?> -->
                                    </td>
                                    <td>
                                        <?php
                                            echo CURRENCY . number_format($salary, 2);
                                        ?>
                                    </td>
                                    <td><?php echo CURRENCY . number_format($res['semi_gross_profit'], 2); ?></td>
                                    <td>
                                        <?php 
                                                echo CURRENCY . number_format($filter, 2);
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    // Enable Bootstrap dropdown for the category-dropdown
    var categoryDropdown = document.querySelector('.sales-dropdown');
    categoryDropdown.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var parent = categoryDropdown.parentElement;
        var menu = parent.querySelector('.dropdown-menu');
        var isOpen = menu.classList.contains('show');

        // Close all other open dropdowns
        var otherDropdowns = document.querySelectorAll('.dropdown-centered');
        otherDropdowns.forEach(function(otherDropdown) {
            var otherParent = otherDropdown;
            var otherMenu = otherParent.querySelector('.dropdown-menu');
            if (otherParent !== parent && otherMenu.classList.contains('show')) {
                otherMenu.classList.remove('show');
            }
        });

        // Toggle the 'show' class for the category-dropdown
        if (isOpen) {
            menu.classList.remove('show');
        } else {
            menu.classList.add('show');
        }
    });

    // Close dropdowns when clicking outside (for all dropdowns)
    document.addEventListener('click', function(event) {
        var dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(function(menu) {
            if (menu.classList.contains('show') && !menu.contains(event.target)) {
                menu.classList.remove('show');
            }
        });
    });
</script>
</html>