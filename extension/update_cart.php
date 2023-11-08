<?php
require_once "../includes/db_conn.php";

$order_qty_post = $_POST['order_qty'];
$cart_id = $_POST['cart_id'];

$sql_cart = "SELECT c.order_qty, p.product_stock, c.product_id
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.cart_id = ?";
$stmt_cart = mysqli_prepare($conn, $sql_cart);
mysqli_stmt_bind_param($stmt_cart, "i", $cart_id);
mysqli_stmt_execute($stmt_cart);
mysqli_stmt_store_result($stmt_cart);

if (mysqli_stmt_num_rows($stmt_cart) == 1) {
    mysqli_stmt_bind_result($stmt_cart, $order_qty_cart, $product_stock, $product_id);
    mysqli_stmt_fetch($stmt_cart);

    $qty_difference = $order_qty_post - $order_qty_cart;

    if ($qty_difference > 0) {
        // If increasing order_qty, calculate the maximum order_qty that can be added
        $max_order_qty = min($qty_difference, $product_stock);
    } else {
        // If reducing order_qty, calculate the maximum order_qty that can be reduced
        $max_order_qty = max($qty_difference, -$order_qty_cart);
    }
    if ($max_order_qty != $qty_difference) {
        $_SESSION['show_toast'] = true;
    } 
    // Update the cart with the new order_qty
    $new_order_qty = $order_qty_cart + $max_order_qty;
    $sql_update_cart = "UPDATE cart SET order_qty = ? WHERE cart_id = ?";
    $stmt_update_cart = mysqli_prepare($conn, $sql_update_cart);
    mysqli_stmt_bind_param($stmt_update_cart, "ii", $new_order_qty, $cart_id);
    mysqli_stmt_execute($stmt_update_cart);

    if (mysqli_stmt_affected_rows($stmt_update_cart) > 0) {
        // Update the product_stock based on the difference
        $sql_update_stock = "UPDATE products SET product_stock = product_stock - ? WHERE product_id = ?";

        $stmt_update_stock = mysqli_prepare($conn, $sql_update_stock);
        mysqli_stmt_bind_param($stmt_update_stock, "ii", $max_order_qty, $product_id);

        if ($stmt_update_stock) {
            mysqli_stmt_execute($stmt_update_stock);

            if (mysqli_stmt_affected_rows($stmt_update_stock) > 0) {
                header('Location: ../pos.php');
            } else {
                echo "Error updating product_stock: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt_update_stock);
        } else {
            echo "Error preparing product_stock update statement: " . mysqli_error($conn);
        }
    } else {
        echo "Error updating cart quantity: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt_update_cart);
} else {
    echo "Cart item not found.";
}

mysqli_close($conn);
?>
