<style>
    .cart-con {
        background-color: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .row-divider {
        border-bottom: 2px solid #ccc; /* Add a border at the bottom of the row */
        margin-bottom: 10px; /* Add some spacing between the row and the container */
        padding-bottom: 10px; 
    }

    .table-cart {
        height: 21rem;
        overflow-y:scroll;
        display: block;
    }
    .table-cart::-webkit-scrollbar {
    width: 12px; /* Width of the scrollbar */
    }

    .table-cart::-webkit-scrollbar-thumb {
    background-color: #1111;
    border-radius: 6px;
    }

    .table-cart::-webkit-scrollbar-thumb:hover {
    background-color: #555;
    }

    .table-cart::-webkit-scrollbar-thumb:active {
    background-color: #111;
    }
</style>
    <?php
    $sql = "SELECT * FROM users WHERE user_id = $user_id";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo "Error: " . mysqli_error($conn);
    } else {
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $username = $row['username'];
            $user_type = $row['user_type'];
            ?>
            <div class="container-fluid cart-con">
                <div class="input-group mb-1 p-1 pt-2">
                    <span class="input-group-text" id="username"><ion-icon name="person-sharp"></ion-icon></span>
                    <input type="readonly" class="form-control form-control-sm" name="user" value="<?php echo $username . '-' . $user_type; ?>">
                </div>
            <?php
                }
            }
            ?>
            <div class="container">
                <div class="table-responsive">
                    <table class="table table-borderless table-cart row-divider">
                        <thead class="table-light">
                            <tr>
                                <th></th>
                                <th>Product Name</th>
                                <th>Product Price</th>
                                <th>Product Qty</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sqlcart = "SELECT * FROM cart WHERE user_id = $user_id";
                            $resultcart = mysqli_query($conn, $sqlcart);
                            $total_sum = 0;

                            if (!$resultcart) {
                                echo "Error: " . mysqli_error($conn);
                            } else {
                                while ($row = mysqli_fetch_assoc($resultcart)) {
                                    $total_amount = $row['product_price'] * $row['order_qty'];
                                    $total_sum += $total_amount;    
                            ?>
                            <tr>
                                <td><form action="" method="POST">   
                                        <input type="hidden" name="cart_id" value="<?php echo $row['cart_id'];?>">
                                        <button type="submit" class="btn btn-sm" name="delete-cart"><i class='bx bx-minus-circle'></i></button>
                                    </form>
                                </td>
                                <td>
                                    <?php
                                        $unitsql = "SELECT product_unit FROM products WHERE product_id = {$row['product_id']}";
                                        $unitres = mysqli_query($conn, $unitsql);

                                        if ($unitres) {
                                            $unitData = mysqli_fetch_assoc($unitres); // Fetch the result as an associative array
                                            if ($unitData) {
                                                echo $row['product_name'] . '|' . $unitData['product_unit'];
                                            } else {
                                                echo "No unit data found for the product.";
                                            }
                                        } else {
                                            echo "Error fetching unit data: " . mysqli_error($conn);
                                        }
                                    ?>
                                </td>
                                <td><?php echo number_format($row['product_price'],2); ?></td>
                                <td>
                                <form action="./extension/update_cart.php" method="POST">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['cart_id'];?>">
                                    <input class="form-control form-control-sm" type="number" name="order_qty" value="<?php echo $row['order_qty']; ?>" onchange="this.form.submit()">
                                </form>
                                </td>
                                <td><?php echo CURRENCY . number_format($total_amount, 2)?></td>
                            </tr>
                            <?php
                                } 
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="totalamt float-end d-flex">
                        <p style="padding-right: 5em;"><b>TOTAL AMOUNT</b></p>
                        <p style="padding-right: 3em;"><?php echo CURRENCY . number_format($total_sum,2)?></p>
                    </div>
                </div>
                <form action="" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <div class="row">
                        <div class="col-md-5 mb-1">
                            <label for="customer" class="form-label">Customer Name</label>
                            <input type="text" class="form-control form-control-sm" id="customer" name="customer">
                        </div>
                        <div class="col-md-7 mb-1">
                            <label for="cus.address" class="form-label">Address</label>
                            <input type="text" class="form-control form-control-sm" id="cus.address" name="cus.address">
                        </div>
                    </div>
                    <div class="row row-divider">
                        <div class="col-md-4 mb-2 mt-0">
                            <label for="payment_method">Payment Method:</label>
                            <select class="form-control" id="payment_method" name="payment_method" required onchange="showReferenceNumberInput()">
                                <option value="Cash">Cash</option>
                                <option value="Gcash">Gcash</option>
                                <option value="PayMaya">PayMaya</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 mt-0" id="referenceNumberInput" style="display: none;">
                            <label for="reference_number">Reference Number:</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number">
                        </div>
                        <script>
                            function showReferenceNumberInput() {
                            var paymentMethod = document.getElementById("payment_method");
                                var referenceNumberInput = document.getElementById("referenceNumberInput");

                                if (paymentMethod.value === "Gcash" || paymentMethod.value === "PayMaya") {
                                    referenceNumberInput.style.display = "block";
                                } else {
                                    referenceNumberInput.style.display = "none";
                                }
                            }
                        </script>
                        <div class="col-md-4 mb-2 mt-0">
                            <label for="tin" class="form-label">TIN:</label>
                            <input type="text" class="form-control form-control-sm" id="tin" name="tin">
                        </div>
                        <div class="col-md-4 mb-2 mt-0">
                            <label for="buss_style" class="form-label">Buss. Style:</label>
                            <input type="text" class="form-control form-control-sm" id="buss_style" name="buss_style">
                        </div>
                    </div>
                    <div class="button text-end">
                        <button type="submit" class="btn btn-outline-danger" name="reset">Reset</button>
                        <button type="submit" class="btn btn-secondary2" name="checkout">CHECKOUT</button>
                    </div>
                </form>
            </div>
        </div>