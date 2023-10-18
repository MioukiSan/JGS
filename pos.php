<?php
    require_once './includes/db_conn.php';
    require_once './includes/restriction.php';
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['addcart'])) {
        $product_id = $_POST['product_id'];
    
        // Check if the product already exists in the cart for the current user
        $existing_cart_query = "SELECT cart_id, order_qty FROM cart WHERE user_id = ? AND product_id = ?";
        $existing_cart_stmt = mysqli_prepare($conn, $existing_cart_query);
        mysqli_stmt_bind_param($existing_cart_stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($existing_cart_stmt);
        $existing_cart_result = mysqli_stmt_get_result($existing_cart_stmt);
        $existing_cart_data = mysqli_fetch_assoc($existing_cart_result);
    
        if ($existing_cart_data) {
            // Product exists in the cart, update the quantity
            $new_quantity = $existing_cart_data['order_qty'] + 1;
    
            // Check if there is enough stock for the new quantity
            $check_stock_query = "SELECT product_stock FROM products WHERE product_id = ?";
            $check_stock_stmt = mysqli_prepare($conn, $check_stock_query);
            mysqli_stmt_bind_param($check_stock_stmt, "i", $product_id);
            mysqli_stmt_execute($check_stock_stmt);
            $check_stock_result = mysqli_stmt_get_result($check_stock_stmt);
            $check_stock_data = mysqli_fetch_assoc($check_stock_result);
    
            if ($check_stock_data && $new_quantity <= $check_stock_data['product_stock']) {
                $update_query = "UPDATE cart SET order_qty = ? WHERE cart_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $existing_cart_data['cart_id']);
                $update_success = mysqli_stmt_execute($update_stmt);
    
                if ($update_success) {
                    // Reduce product_stock
                    $reduce_stock_query = "UPDATE products SET product_stock = product_stock - 1 WHERE product_id = ?";
                    $reduce_stock_stmt = mysqli_prepare($conn, $reduce_stock_query);
                    mysqli_stmt_bind_param($reduce_stock_stmt, "i", $product_id);
                    mysqli_stmt_execute($reduce_stock_stmt);
    
                } else {
                    $error_message = "Failed to update item quantity in cart.";
                }
            } else {
                $error_message = "Not enough stock to update the item quantity.";
            }
        } else {
            // Product does not exist in the cart, insert a new record
            $product_query = "SELECT item_name, retail_price, product_stock FROM products WHERE product_id = ?";
            $product_stmt = mysqli_prepare($conn, $product_query);
            mysqli_stmt_bind_param($product_stmt, "i", $product_id);
            mysqli_stmt_execute($product_stmt);
            $product_result = mysqli_stmt_get_result($product_stmt);
            $product_data = mysqli_fetch_assoc($product_result);
    
            if ($product_data && $product_data['product_stock'] >= 1) {
                $insert_query = "INSERT INTO cart (user_id, product_id, product_name, product_price, order_qty)
                                VALUES (?, ?, ?, ?, 1)";
                $insert_stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($insert_stmt, "iiss", $user_id, $product_id, $product_data['item_name'], $product_data['retail_price']);
                $insert_success = mysqli_stmt_execute($insert_stmt);
    
                if ($insert_success) {
                    // Reduce product_stock
                    $reduce_stock_query = "UPDATE products SET product_stock = product_stock - 1 WHERE product_id = ?";
                    $reduce_stock_stmt = mysqli_prepare($conn, $reduce_stock_query);
                    mysqli_stmt_bind_param($reduce_stock_stmt, "i", $product_id);
                    mysqli_stmt_execute($reduce_stock_stmt);
    
                    header("Location: pos.php");
                    exit();
                } else {
                    $error_message = "Failed to add item to cart.";
                }
            } else {
                $error_message = "Not enough stock to add the item to the cart.";
            }
        }
    }
    
    if (isset($_POST['delete-cart'])) {
        $d_cart_id = $_POST['cart_id'];
    
        // Retrieve the quantity from the cart
        $get_quantity_query = "SELECT product_id, order_qty FROM cart WHERE cart_id = ?";
        $get_quantity_stmt = mysqli_prepare($conn, $get_quantity_query);
        mysqli_stmt_bind_param($get_quantity_stmt, "i", $d_cart_id);
        mysqli_stmt_execute($get_quantity_stmt);
        $get_quantity_result = mysqli_stmt_get_result($get_quantity_stmt);
        $get_quantity_data = mysqli_fetch_assoc($get_quantity_result);
    
        if ($get_quantity_data) {
            $deleted_quantity = $get_quantity_data['order_qty'];
            $product_id = $get_quantity_data['product_id'];
    
            // Delete the item from the cart
            $delete_query = "DELETE FROM cart WHERE cart_id = ?";
            $delete_stmt = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($delete_stmt, "i", $d_cart_id);
            $delete_success = mysqli_stmt_execute($delete_stmt);
    
            if ($delete_success) {
                // Add the deleted quantity back to product_stock
                $add_to_stock_query = "UPDATE products SET product_stock = product_stock + ? WHERE product_id = ?";
                $add_to_stock_stmt = mysqli_prepare($conn, $add_to_stock_query);
                mysqli_stmt_bind_param($add_to_stock_stmt, "ii", $deleted_quantity, $product_id);
                mysqli_stmt_execute($add_to_stock_stmt);
    
                header("location: pos.php?user_delete=done");
                exit();
            } else {
                header("location: pos.php?user_delete=failed");
                exit();
            }
        } else {
            // Cart item not found, handle the error as needed
        }
    }

    if (isset($_POST['checkout'])) {
        $user_id = $_POST['user_id'];
        $payment_method = $_POST['payment_method'];
        $customer = isset($_POST['customer']) ? $_POST['customer'] : 'Guest';
        $customer_address = isset($_POST['cus_address']) ? $_POST['cus_address'] : '';
        $tin = $_POST['tin'];
        $buss = $_POST['buss_style'];
        
        // Generate a single sales_transaction_code for the entire checkout batch
        $sales_transaction_code = generateSalesTransactionCode();
        
        // Calculate total profit for all products in the cart
        $total_profit = 0;
        $sqlcart = "SELECT * FROM cart WHERE user_id = $user_id";
        $resultcart = mysqli_query($conn, $sqlcart);
        
        if ($resultcart) {
            while ($row = mysqli_fetch_assoc($resultcart)) {
                $product_id = $row['product_id'];
                $order_qty = $row['order_qty'];
        
                // Retrieve the actual_price and retail_price from the inventory
                $inventory_query = "SELECT actual_price, retail_price FROM products WHERE product_id = $product_id";
                $inventory_result = mysqli_query($conn, $inventory_query);
        
                if ($inventory_result && $inventory_row = mysqli_fetch_assoc($inventory_result)) {
                    $actual_price = $inventory_row['actual_price'];
                    $retail_price = $inventory_row['retail_price'];
        
                    // Calculate product_profit for this product
                    $product_profit = ($retail_price - $actual_price) * $order_qty;
                    $total_profit += $product_profit;
        
                    // Insert data into the sales table for each product with the same transaction code
                    $insert_query = "INSERT INTO sales (sales_transaction_code, user_id, product_id, total_amt, product_profit, product_qty, sale_date, employee_id, payment_method, customer_name, cust_address, tin, buss_style)
                 VALUES ('$sales_transaction_code', $user_id, $product_id, $retail_price * $order_qty, '$product_profit', $order_qty, NOW(), $user_id, '$payment_method', '$customer', '$customer_address', '$tin', '$buss')";
                    
                    if (!mysqli_query($conn, $insert_query)) {
                        echo "Error inserting data into sales table: " . mysqli_error($conn);
                    }
                }
            }
        
            // Clear the cart for this user after successful checkout
            $clear_cart_query = "DELETE FROM cart WHERE user_id = $user_id";
            if (!mysqli_query($conn, $clear_cart_query)) {
                echo "Error clearing cart: " . mysqli_error($conn);
            } else {
                echo "Checkout successful!";
                $transaction_code = $sales_transaction_code; 
                $invoiceURL = "./extension/invoice_print.php?transaction_code=" . $transaction_code;
            
                // Use JavaScript to open the invoice.php page in a new tab
                echo '<script type="text/javascript">
                    var invoiceURL = "' . $invoiceURL . '";
                    window.open(invoiceURL, "_blank");
                </script>';
            } 
        } else {
            echo "Error fetching cart data: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['reset'])) {
        $user_id = $_POST['user_id'];
        $delete_query = "DELETE FROM cart WHERE user_id = ?";
   
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
    
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Cart items for user with user_id $user_id(YOUR ID) have been deleted.')</script>";
        } else {
            echo "Error deleting cart items: " . mysqli_error($conn);
        }
    
        mysqli_stmt_close($stmt);
    }
    
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale</title>
    <?php include_once './includes/head.php'; ?>  
</head>
<body class="bg-light pad">
    <?php include_once './includes/side_navbar.php'; ?>
    <div class="container">
        <div class="row pt-4">
        <div class="col-md-7 col-sm-7 bg-white shadow-sm border" style="font-size: 13px;">
                <div class="row">
                    <div class="col-6 mb-1 mt-2 search">
                        <form method="GET" action="?search_query" role="search" class="d-flex">
                            <input type="search" placeholder="Search" class="form-control search me-2" name="search_query" aria-label="search">
                            <button class="btn btn-secondary1" type="submit">Search</button>
                        </form>
                    </div>
                    <div class="table-responsive">
                    <table class="table text-center">
                        <thead class="table table-primary">
                            <tr>
                                <th>Item Name</th>
                                <th>Price</th>
                                <th>Product Stock</th>
                                <th>Item Category</th>
                                <th>Item Description</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number
                            $rowsPerPage = 10; 

                            if (isset($_GET['search_query'])) {
                                $searchkey = $_GET['search_query'];

                                $totalRowsQuery = "SELECT COUNT(*) as count FROM products 
                                                WHERE (item_name LIKE ? OR item_details LIKE ? OR item_category LIKE ?)
                                                AND item_status = 'A'";
                                $stmt = mysqli_prepare($conn, $totalRowsQuery);
                                $searchkeyWithWildcards = '%' . $searchkey . '%';
                                mysqli_stmt_bind_param($stmt, "sss", $searchkeyWithWildcards, $searchkeyWithWildcards, $searchkeyWithWildcards);
                                mysqli_stmt_execute($stmt);
                                $totalRowsResult = mysqli_stmt_get_result($stmt);
                                $totalRows = mysqli_fetch_assoc($totalRowsResult)['count'];
                            } else {
                                $totalRowsQuery = "SELECT COUNT(*) as count FROM products WHERE item_status = 'A' and product_stock != 0";
                                $totalRowsResult = mysqli_query($conn, $totalRowsQuery);
                                $totalRows = mysqli_fetch_assoc($totalRowsResult)['count'];
                            }

                            $totalPages = ceil($totalRows / $rowsPerPage); // Calculate total pages
                            $startFrom = ($page - 1) * $rowsPerPage; // Calculate the starting index for the current page

                            if (isset($_GET['search_query'])) {
                                $query = "SELECT * FROM products 
                                        WHERE (item_name LIKE ? OR item_details LIKE ? OR item_category LIKE ?)
                                        AND item_status = 'A'
                                        LIMIT ?, ?";
                                $stmt = mysqli_prepare($conn, $query);
                                $searchkeyWithWildcards = '%' . $searchkey . '%';
                                mysqli_stmt_bind_param($stmt, "sssss", $searchkeyWithWildcards, $searchkeyWithWildcards, $searchkeyWithWildcards, $startFrom, $rowsPerPage);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                            } else {
                                $query = "SELECT * FROM products 
                                        WHERE item_status = 'A' and product_stock != 0
                                        LIMIT ?, ?";
                                $stmt = mysqli_prepare($conn, $query);
                                mysqli_stmt_bind_param($stmt, "ii", $startFrom, $rowsPerPage);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                            }

                            foreach ($result as $items => $row) {
                            ?>
                            <tr>
                                <td><?php echo $row['item_name']; ?></td>
                                <td><?php echo CURRENCY . number_format($row['retail_price'], 2); ?></td>
                                <td><?php echo $row['product_stock']; ?></td>
                                <td><?php echo $row['item_category']; ?></td>
                                <td><?php echo $row['item_details']; ?></td>
                                <td></td>
                                <td>
                                    <form action="" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                        <input type="hidden" name="item_name" value="<?php echo $row['item_name']; ?>">
                                        <input type="hidden" name="retail_price" value="<?php echo $row['retail_price']; ?>">
                                        <button type="submit" class="btn btn-info btn-sm" name="addcart"><i class='bx bxs-add-to-queue'></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php 
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">Previous</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php } ?>
                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="col-md-5 col-sm-5">
                <?php
                    require_once 'cart.php';
                ?>
        </div>
    </div>
</body>
</html>