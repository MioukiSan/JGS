<?php 
    require_once './includes/db_conn.php';
    require_once './includes/restriction.php';
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    
        // Check the user's user_type
        $check_user_query = "SELECT user_type FROM users WHERE user_id = ?";
        $result = query($conn, $check_user_query, [$user_id]); // Replace 'query' with your database query function
    
        if (count($result) > 0) {
            $user_type = $result[0]['user_type'];
    
            // Check if the user_type is "Cashier"
            if ($user_type === "cashier") {
                // Redirect the Cashier to the dashboard or the last visited page
                if (isset($_SESSION['last_visited_url'])) {
                    header("Location: " . $_SESSION['last_visited_url']);
                } else {
                    header("Location: pos.php"); // Redirect to the dashboard if no last visited URL is available
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
    <title>SALES HISTORY</title>
    <?php include_once './includes/head.php'; ?>
</head>
<body class="bg-light pad">
    <?php include_once './includes/side_navbar.php'; ?>
    <div class="container">
        <div class="row pt-1">
            <form method="GET" action="?search_query" role="search" class="d-flex">
                <input type="search" placeholder="Search date or Transaction Code eg.(0000-00-00/yyyy-mm-dd or JGSXXXXX)" class="form-control search me-2 header-con d-flex" name="search_query" aria-label="search">
                <button class="btn header-con1" type="submit">Search</button>
            </form>
            <form action="" method="POST">
                <div class="input-group mb-3 header-con">
                    <span class="input-group-text">For These Date</span>
                    <input type="date" name="this_date" class="form-control">
                    <input type="submit" name="filterdate" value="Filter" class="btn btn-secondary1">
                </div>
            </form>
            <div class="col-12">
                <div class="table table-responsive bg-white text-center">
                    <table class="table table-responsive table-borderless">
                        <thead class="table-dark">
                            <th>Transaction Code</th>
                            <th>Total Price</th>
                            <th>Transaction Date</th>
                            <th>Transaction Details</th>
                            <th></th>
                        </thead>
                        <tbody>
                        <?php
                            if(isset($_GET['search_query'])){
                                $search_query = $_GET['search_query'];
                                $searchSQL = "SELECT 
                                    sales_transaction_code as transactioncode,  
                                    DATE(sale_date) as date_order,
                                    SUM(total_amt) as total_sale
                                    FROM sales
                                    WHERE sales_transaction_code LIKE '%$search_query%' OR DATE(sale_date) = '$search_query'
                                    GROUP BY sales_transaction_code, date_order
                                    ORDER BY date_order DESC";
                                $resultsales = query($conn, $searchSQL);
                            }                            
                            elseif(isset($_POST['filterdate'])){
                                $selected_date = $_POST['this_date'];
                                $filterDateSQL = "SELECT 
                                    sales_transaction_code as transactioncode,  
                                    DATE(sale_date) as date_order,
                                    SUM(total_amt) as total_sale
                                    FROM sales
                                    WHERE DATE(sale_date) = '$selected_date'
                                    GROUP BY sales_transaction_code, date_order
                                    ORDER BY date_order DESC";
                                $resultsales = query($conn, $filterDateSQL);
                            }
                            else{
                                $salesSQL = "SELECT 
                                    sales_transaction_code as transactioncode,  
                                    DATE(sale_date) as date_order,
                                    SUM(total_amt) as total_sale
                                    FROM sales
                                    GROUP BY sales_transaction_code, date_order
                                    ORDER BY date_order DESC";
                                $resultsales = query($conn, $salesSQL);
                            }                            
                            foreach($resultsales as $res) {
                            ?>
                                <tr>
                                    <td><?php echo $res['transactioncode']; ?></td>
                                    <td><?php echo CURRENCY . number_format($res['total_sale'], 2); ?></td>
                                    <td><?php echo $res['date_order']; ?></td>
                                    <td>
                                        <?php 
                                            $rep_detail_sql = "SELECT p.item_name, s.product_qty, s.total_amt
                                                FROM sales s
                                                JOIN products p ON s.product_id = p.product_id
                                                WHERE s.sales_transaction_code = ?";
                                                $rep_detail = query($conn, $rep_detail_sql, array($res['transactioncode']));
                          
                                        ?>
                                        <button class="btn btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $res['transactioncode'];?>" aria-expanded="false" aria-controls="<?php echo $res['transactioncode'];?>">
                                            <ion-icon name="bag-outline"></ion-icon>Item Sold
                                        </button>
                                        <div class="collapse" id="<?php echo $res['transactioncode'];?>">
                                            <table class="table table-responsive table-striped">
                                                <tr>
                                                    <th></th>
                                                    <th>Orders</th>
                                                    <th>Sales Amount</th>
                                                </tr>
                                                <?php foreach($rep_detail as $id){ ?>
                                                    <tr>
                                                        <td><?php echo $id['item_name'];?></td>
                                                        <td><?php echo $id['product_qty'];?></td>
                                                        <td><?php echo CURRENCY . number_format($id['total_amt'], 2);?></td>
                                                    </tr>
                                                <?php }?>
                                            </table>
                                        </div>
                                    </td>
                                    <td>
                                        <form method="POST" action="./extension/invoice.php" target="_blank">
                                            <input type="hidden" name="transaction_code" value="<?php echo $res['transactioncode']; ?>">
                                            <button type="submit" id="invoiceForm" name="invoice" class="btn btn-info"><i class='bx bxs-printer'></i></button>
                                            <!-- <button type="submit" name="action" class="btn btn-success"><i class='bx bxs-download'></i> </button> -->
                                        </form>
                                        <script>
                                            document.getElementById('invoiceForm').addEventListener('submit', function (e) {
                                            });
                                        </script>
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

</html>