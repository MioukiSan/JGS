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
    <title>Dashboard</title>
    <?php include_once './includes/head.php'; ?>
</head>
<body class="bg-light pad">
    <?php include_once './includes/side_navbar.php'; ?>
    <div class="container">
        <div class="row pt-4">
            <div class="col-xl-4 col-md-4">
                <?php
                    $rtc = date('Y-m-d');
                    $sql_today = "SELECT 
                        DATE(s.sale_date) as date_order,
                        SUM(s.total_amt) AS total_sale,
                        SUM(s.product_profit) AS semi_gross_profit,
                        (SELECT SUM(a.salary) FROM attendance a WHERE DATE(a.date) = ?) AS total_salary
                        FROM sales s
                        WHERE DATE(s.sale_date) = ? 
                        GROUP BY date_order
                        ORDER BY date_order DESC";

                    $res_today = query($conn, $sql_today, array($rtc, $rtc));

                    if (!empty($res_today)) {
                        $today = $res_today[0];
                        $targettoday = 10000;
                        $today_perc = ($today['total_sale'] / $targettoday) * 100;
                        $profit = $today['semi_gross_profit'] - $today['total_salary'];
                    } else {
                        $today = array(
                            'total_sale' => 0, 
                            'total_salary' => 0,
                            'semi_gross_profit' => 0,
                        );
                        $today_perc = 0;
                        $profit = 0;
                    }
                ?>
                <div class="card shadow-sm py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="font-weight-bold text-primary text-uppercase mb-1" style="font-size: small;">
                                    Revenue(TODAY)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" style="font-size: x-large;"><?php echo CURRENCY . number_format($today['total_sale'], 2); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class='bx bxs-coin bx-lg' style='color:#0b50f5'  ></i>
                            </div>
                            <!-- <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar <?php if ($today_perc < 25) { echo "text-bg-danger"; } else if ($today_perc < 65) { echo "text-bg-warning"; } else if ($today_perc < 95) { echo "text-bg-info"; } else { echo "text-bg-success"; } ?>" style="width: <?php echo $today_perc; ?>%">
                                    <?php echo $today_perc; ?>%
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <?php
                $currentYear = date('Y');
                $currentMonth = date('m');
                $startOfMonth = "{$currentYear}-{$currentMonth}-01";
                $endOfMonth = date('Y-m-t', strtotime($startOfMonth));

                $revenueSQL = "SELECT
                    DATE_FORMAT(s.sale_date, '%Y-%m') AS month_year,
                    SUM(s.total_amt) AS total_sale,
                    SUM(s.product_profit) AS semi_gross_profit,
                    (SELECT SUM(a.salary) FROM attendance a WHERE DATE(a.date) BETWEEN '$startOfMonth' AND '$endOfMonth') AS total_salary
                    FROM sales s
                    WHERE s.sale_date BETWEEN '$startOfMonth' AND '$endOfMonth'
                    GROUP BY month_year
                    ORDER BY month_year DESC";

                $revenuemonth = query($conn, $revenueSQL);
                $salesMONTH = 0;

                foreach ($revenuemonth as $month) {
                    $salesMONTH = $month['total_sale'];
                }
            ?>
            <div class="col-xl-4 col-md-4">
                <div class="card shadow-sm py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="font-weight-bold text-success text-uppercase mb-1" style="font-size: small;">
                                REVENUE(MONTHLY)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" style="font-size: x-large;"><?php echo CURRENCY . number_format($salesMONTH, 2)?></div>
                            </div>
                            <div class="col-auto">
                            <i class='bx bx-money bx-lg' style='color:#00FF14' ></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4">
                <div class="card shadow-sm py-3">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: small;">
                                PROFIT(TODAY)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" style="font-size: x-large;"><?php echo CURRENCY . number_format($profit, 2); ?></div>
                            </div>
                            <div class="col-auto">
                            <i class='bx bxs-report bx-lg' style='color:#FFE200' ></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-5 mt-3">
            <div class="col-xl-8 bg-white shadow-sm card pt-3">
                <form action="" method="POST">
                <div class="input-group mb-3">
                    <span class="input-group-text">Between </span>
                    <input type="date" name="start_date" class="form-control">
                    <span class="input-group-text"> and </span>
                    <input type="date" name="end_date" class="form-control">
                    <input type="submit" name="filter_range" value="filter" class="btn btn-primary">
                </div>
                </form>
                <form action="" method="POST">
                <div class="row">
                    <div class="input-group mb-3">
                        <span class="input-group-text">For These Date</span>
                        <input type="date" name="this_date" class="form-control">
                        <input type="submit" name="filter_date" value="Filter" class="btn btn-primary">
                    </div>
                </div>
                <div class="row dash-scroll row-cols-3 row-cols-md-5 g-4">
                <?php
                    if (isset($_POST['filter_date'])) {
                        // Filter by date
                        $salesSQL = "SELECT
                            DATE(s.sale_date) as date_order,
                            SUM(s.total_amt) AS total_sale,
                            SUM(s.product_profit) AS semi_gross_profit
                            FROM sales s
                            WHERE DATE(s.sale_date) = ? 
                            GROUP BY date_order
                            ORDER BY date_order DESC";
                        $resultsales = query($conn, $salesSQL, array($_POST['this_date']));
                    } elseif (isset($_POST['filter_range'])) {
                        // Filter by date range
                        $salesSQL = "SELECT
                            DATE(s.sale_date) as date_order,
                            SUM(s.total_amt) AS total_sale,
                            SUM(s.product_profit) AS semi_gross_profit
                            FROM sales s
                            WHERE s.sale_date BETWEEN ? AND ?
                            GROUP BY DATE(s.sale_date)
                            ORDER BY date_order ASC";
                        $resultsales = query($conn, $salesSQL, array($_POST['start_date'], $_POST['end_date']));
                    } else {
                        // Default code if none of the filters are selected
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

                    foreach ($resultsales as $res) {
                        $target = 1000;
                        $order_perc = ($res['salexdate'] / $target) * 100;
                        ?>
                        <div class="col-xl-4 col-md-4">
                            <div class="card mt-2 p-2">
                                <b class="ms-2"><?php echo $res['date_order']; ?></b>
                                <table class="table table-responsive table-hover">
                                    <tr>
                                        <td>Total Sales</td>
                                        <td><?php echo CURRENCY . number_format($res['total_sale'], 2); ?></td>
                                    </tr>
                                </table>
                                <?php
                                    $rep_detail_sql = "SELECT p.item_name, s.product_qty, s.total_amt
                                    FROM sales s
                                    INNER JOIN products p ON s.product_id = p.product_id
                                    WHERE DATE(s.sale_date) = ?";
                                    $rep_detail = query($conn, $rep_detail_sql, array($res['date_order']));
                                ?>
                                <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#<?php echo $res['date_order']; ?>" aria-expanded="false"
                                    aria-controls="<?php echo $res['date_order']; ?>">
                                    Item Sold
                                </button>
                                <div class="collapse multicollapse" id="<?php echo $res['date_order']; ?>">
                                    <table class="table table-responsive table-striped">
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Orders</th>
                                            <th>Sales Amt</th>
                                        </tr>
                                        <?php
                                        foreach ($rep_detail as $item) {
                                            ?>
                                            <tr>
                                                <td><?php echo $item['item_name']; ?></td>
                                                <td><?php echo $item['product_qty']; ?></td>
                                                <td><?php echo CURRENCY . number_format($item['total_amt'], 2); ?></td>
                                            </tr>
                                        <?php
                                    }
                                    ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
             </div>
            <div class="col-xl-4 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="text-center" style="background-color: #1C387C; color: white;">PRODUCTS</h4>
                        <div class="table table-responsive-sm text-center">
                            <table class="table caption-top table-striped">
                                <caption>RESTOCK NEEDED</caption>
                                <thead class="table-primary">
                                    <tr>
                                        <td>NAME</td>
                                        <td>STOCK</td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $restock = "SELECT * FROM products WHERE product_stock <= warning_stock";
                                        $restockprod = mysqli_query($conn, $restock);
                                        
                                        while($resultprod = mysqli_fetch_assoc($restockprod)){
                                    ?>
                                    <tr>
                                        <td><?php echo $resultprod['item_name'] ?></td>
                                        <td><?php echo $resultprod['product_stock'] ?></td>
                                        <td>
                                            <form action="inventory.php" method="POST">
                                                <input type="hidden" name="product_id" value="<?php echo $resultprod['product_id'] ?>">
                                                <button class="btn" type="submit" name="navigate"><ion-icon name="create-outline"></ion-icon></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mt-3 bg-white shadow-sm">
                    <div class="card-body">
                        <h3 class="text-center" style="background-color: #1C387C; color: white;">GENERATE DOCUMENTS</h3>
                            <h5 style="color: #1C387C; padding-top: 1em;">GENERATE SALES REPORT</h5>
                            <form action="" method="POST">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Between</span>
                                    <input type="date" name="start_date" class="form-control" required>
                                    <span class="input-group-text"> and </span>
                                    <input type="date" name="end_date" class="form-control" required>
                                    <button type="submit" class="text-center btn btn-secondary1" name="generaterep">GENERATE</button>
                                </div>
                            </form>
                            <?php
                                if(isset($_POST['generaterep'])){
                                    $start_date = $_POST['start_date'];
                                    $end_date = $_POST['end_date'];
                                    $showViewButton = true;
                                } else {
                                    $showViewButton = false; 
                                }
                            ?>

                            <form action="./extension/generate_sales_rep.php" method="POST" target="_blank">
                                <?php if($showViewButton): ?>
                                <div class="input-group mb-3 text-center">
                                    <input type="text" name="start_date" class="form-control" value="<?php echo $start_date; ?>" aria-label="Username">
                                    <span class="input-group-text">BETWEEN</span>
                                    <input type="text" name="end_date" class="form-control" value="<?php echo $end_date; ?>" aria-label="Server">
                                </div>
                                <div class="text-center">
                                    <button type="submit" id="invoiceRep" class="text-center btn btn-secondary1" name="viewrep"><i class='bx bxs-printer'></i> VIEW</button>
                                    <script>
                                        document.getElementById('invoiceRep').addEventListener('submit', function (e) {
                                        });
                                    </script>
                                </div>
                                <?php endif; ?>
                            </form>
                            <h5 style="color: #1C387C;">FINANCIAL REPORT</h5>
                            <form action="" method="POST">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Between</span>
                                    <input type="date" name="start_date" class="form-control" required>
                                    <span class="input-group-text"> and </span>
                                    <input type="date" name="end_date" class="form-control" required>
                                    <button type="submit" class="text-center btn btn-secondary1" name="generatefi">GENERATE</button>
                                </div>
                            </form>
                            <?php
                                if(isset($_POST['generatefi'])){
                                    $start_date = $_POST['start_date'];
                                    $end_date = $_POST['end_date'];
                                    $showViewButton2 = true;
                                } else {
                                    $showViewButton2 = false; 
                                }
                            ?>

                            <form action="./extension/generate_financial_rep.php" method="POST" target="_blank">
                                <?php if($showViewButton2): ?>
                                <div class="input-group mb-3 text-center">
                                    <input type="text" name="start_date" class="form-control" value="<?php echo $start_date; ?>" aria-label="Username">
                                    <span class="input-group-text">BETWEEN</span>
                                    <input type="text" name="end_date" class="form-control" value="<?php echo $end_date; ?>" aria-label="Server">
                                </div>
                                <div class="text-center">
                                    <button type="submit" id="invoiceRep" class="text-center btn btn-secondary1" name="viewrep"><i class='bx bxs-printer'></i> VIEW</button>
                                    <script>
                                        document.getElementById('invoiceRep').addEventListener('submit', function (e) {
                                        });
                                    </script>
                                </div>
                                <?php endif; ?>
                            </form>

                            <h5 style="color: #1C387C; padding-top: .5em;">GENERATE LOG BOOK</h5>
                            <form action="" method="POST">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Between</span>
                                    <input type="date" name="start_date" class="form-control" required>
                                    <span class="input-group-text"> and </span>
                                    <input type="date" name="end_date" class="form-control" required>
                                    <button type="submit" class="text-center btn btn-secondary1" name="generatelog">GENERATE</button>
                                </div>
                            </form>
                            <?php
                                if(isset($_POST['generatelog'])){
                                    $start_date = $_POST['start_date'];
                                    $end_date = $_POST['end_date'];
                                    $showViewButton1 = true;
                                } else {
                                    $showViewButton1 = false; 
                                }
                            ?>

                            <form action="./extension/generate_log.php" method="POST" target="_target">
                                <?php if($showViewButton1): ?>
                                <div class="input-group mb-3 text-center">
                                    <input type="text" name="start_date" class="form-control" value="<?php echo $start_date; ?>" aria-label="Username" >
                                    <span class="input-group-text">BETWEEN</span>
                                    <input type="text" name="end_date" class="form-control" value="<?php echo $end_date; ?>" aria-label="Server">
                                </div>
                                <div class="text-center">
                                    <button type="submit" id="invoiceLog" class="text-center btn btn-secondary1" name="viewlog"><i class='bx bxs-printer'></i> VIEW</button>
                                </div>
                                <script>
                                    document.getElementById('invoiceLog').addEventListener('submit', function (e) {
                                    });
                                </script>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    // Enable Bootstrap dropdown for the my-dropdown
    var myDropdown = document.querySelector('.my-dropdown');
    myDropdown.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var parent = myDropdown.parentElement;
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

        // Toggle the 'show' class for the my-dropdown
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
<footer>
    <?php require_once './includes/footer.php'; ?>
</footer>
</html>