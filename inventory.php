<?php 
    require_once './includes/db_conn.php';
    require_once './includes/restriction.php';
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    
        // Check the user's user_type
        $check_user_query = "SELECT user_type FROM users WHERE user_id = ?";
        $result = query($conn, $check_user_query, [$user_id]); 
    
        if (count($result) > 0) {
            $user_type = $result[0]['user_type'];
    
            // Check if the user_type is "Cashier"
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

    if(isset($_POST['cat'])){
        $catname = $_POST['cat_name'];
        
        $cat = "INSERT INTO categories (category_name) VALUES ('$catname')";
    
        // Execute the query and handle errors
        if (mysqli_query($conn, $cat)) {
            echo "<script>alert('Category inserted successfully.')</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    
    
    if(isset($_POST['add-product'])){
        $item_name = $_POST['item_name'];
        $item_details = $_POST['item_specification'];
        $product_stock = $_POST['product_stock'];
        $actual_price = $_POST['actual_price'];
        $retail_price = $_POST['retail_price'];
        $item_category = $_POST['item_category'];
        $restock = $_POST['restock'];
        $unit = $_POST['unit'];
        $acron = $_POST['acron'];
    
        // Check if a product with the same name and item_details exists
        $existing_product_query = "SELECT * FROM products WHERE item_name = '$item_name' AND item_details = '$item_details'";
        $existing_product_result = mysqli_query($conn, $existing_product_query);
    
        if (mysqli_num_rows($existing_product_result) > 0) {
            // Product with the same name and item_details exists, check prices
            $existing_product_data = mysqli_fetch_assoc($existing_product_result);
            $existing_actual_price = $existing_product_data['actual_price'];
            $existing_retail_price = $existing_product_data['retail_price'];
    
            if ($existing_actual_price != $actual_price || $existing_retail_price != $retail_price) {
                // Prices are different, update the prices
                $update_prices_query = "UPDATE products SET actual_price = $actual_price, retail_price = $retail_price 
                                       WHERE item_name = '$item_name' AND item_details = '$item_details'";
    
                if (mysqli_query($conn, $update_prices_query)) {
                    echo "<script>alert('Product prices updated successfully');</script>.";
                } else {
                    echo "Error updating product prices: " . mysqli_error($conn);
                }
            } else {
                echo "Product with the same name and item details already exists with the same prices.";
            }
        } else {
            // Check if a product with the same name (regardless of item_details) exists
            $existing_name_query = "SELECT * FROM products WHERE item_name = '$item_name'";
            $existing_name_result = mysqli_query($conn, $existing_name_query);
    
            if (mysqli_num_rows($existing_name_result) > 0) {
                echo "<script>alert('Product with the same name exists with different item_details. Adding as a new product.');</script>";
            }
    
            // Insert a new entry
            $insert_query = "INSERT INTO products (item_name, actual_price, retail_price, product_stock, item_category, item_details, warning_stock, product_unit, acronym) 
                             VALUES ('$item_name', $actual_price, $retail_price, $product_stock, '$item_category', '$item_details', '$restock', '$unit', '$acron')";
    
            if (mysqli_query($conn, $insert_query)) {
                // echo "New product added successfully.";
            } else {
                echo "Error adding new product: " . mysqli_error($conn);
            }
        }
    }
    
    if (isset($_POST['delete_cat'])) {
        // Get the category ID to be deleted
        $cat_id_to_delete = $_POST['cat_id'];

        // Check if any products exist with the same category
        $check_query = "SELECT COUNT(*) as product_count FROM products WHERE item_category = (
            SELECT category_name FROM categories WHERE cat_id = $cat_id_to_delete
        )";
        $check_result = mysqli_query($conn, $check_query);

        if ($check_result) {
            $product_count = mysqli_fetch_assoc($check_result)['product_count'];

            if ($product_count > 0) {
                // Products with this category exist, do not delete the category
                echo "<script>alert('Cannot delete category. There are products associated with it.');</script>";
            } else {
                // No products with this category, proceed with deletion
                $delete_query = "DELETE FROM categories WHERE cat_id = $cat_id_to_delete";
                $delete_result = mysqli_query($conn, $delete_query);

                if ($delete_result) {
                    // Category deleted successfully
                    echo "<script>alert('Category deleted successfully');</script>";
                    // You can optionally redirect to another page after deletion
                    // header("Location: your_redirect_url.php");
                } else {
                    // Error occurred while deleting the category
                    echo "<script>alert('Error deleting category');</script>";
                }
            }
        } else {
            // Error occurred while checking for products
            echo "<script>alert('Error checking for products');</script>";
        }
    }


    if(isset($_POST['submit_edit'])) {
        $product_id = $_POST['product_id'];
        $new_item_name = $_POST['new_item_name'];
        $new_actual_price = $_POST['new_actual_price'];
        $new_retail_price = $_POST['new_retail_price'];
        $new_product_stock = $_POST['new_product_stock'];
        $new_item_status = $_POST['new_item_status'];
        $new_item_details = $_POST['new_item_details'];
        $new_acronym = $_POST['acron'];
        $new_unit = $_POST['unit'];
    
        // Update the product details in the database
        $update_query = "UPDATE products 
                         SET item_name = '$new_item_name', 
                             actual_price = $new_actual_price, 
                             retail_price = $new_retail_price, 
                             product_stock = $new_product_stock, 
                             item_status = '$new_item_status', 
                             item_details = '$new_item_details',
                             acronym = '$new_acronym',
                             product_unit = '$new_unit'
                         WHERE product_id = $product_id";
    
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Product details updated successfully.');</script>";
        } else {
            echo "Error updating product details: " . mysqli_error($conn);
        }
    }
    
    if(isset($_POST['submit_delete'])){
        $product_id = $_POST['product_id'];
    
        // Assuming you have a column named 'item_status' in your database
        $update_status_query = "UPDATE products SET item_status = 'I' WHERE product_id = $product_id";
    
        if(mysqli_query($conn, $update_status_query)){
            echo "<script>alert('Item has been marked as inactive.');</script>";
        } else {
            echo "Error marking item as inactive: " . mysqli_error($conn);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVENTORY</title>
    <?php include_once './includes/head.php'; ?>  
</head>
<body class="bg-light pad">
    <?php include_once './includes/side_navbar.php'; ?>
    <div class="container">
        <div class="row mt-4">
            <div class="col-md-12 mt-3">
                <div class="row mb-2">
                    <div class="col-md-10 col-sm-10">
                        <form method="GET" action="?search_query" role="search" class="d-flex">
                            <input type="search" placeholder="Search" class="form-control search me-2" name="search_query" aria-label="search">
                            <button class="btn btn-secondary1 btn-sm" type="submit">Search</button>
                        </form>
                    </div>
                    <div class="col-md-2 d-grid gap-2 d-inline-flex justify-content-md-end">
                        <div class="dropdown-centered d-flex">
                            <button class="btn text-white bg-primary category-dropdown float-end" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                            <ion-icon name="funnel-outline"></ion-icon>
                            </button>
                            <form action="" method="GET">
                                <ul class="dropdown-menu">
                                    <li><button type="submit" class="dropdown-item" name="inactive">Deleted Items</button></li>
                                    <?php
                                        $q = "SELECT * FROM categories";
                                        $qq = mysqli_query($conn, $q);

                                        foreach($qq as $r){
                                            $catname = $r['category_name'];
                                    ?>
                                    <li>
                                        <button type="submit" class="dropdown-item" name="sortBy" value="<?php echo $catname ?>"><?php echo $catname ?></button>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </form>
                        </div>
                            <button type="button" name="add-prod" class="btn bg-primary text-white float-end" data-bs-toggle="modal" data-bs-target="#addproduct">ADD PRODUCT<ion-icon name="add-circle"></ion-icon></i></button>
                            <div class="modal fade" id="addproduct" tabindex="-1" aria-labelledby="adds" aria-hidden="true" data-bs-backdrop="static"  data-bs-keyboard="false">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="adds">ADD PRODUCTS <ion-icon name="construct-outline"></ion-icon></h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                        <form action="" method="POST">
                                            <div class="mb-3">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6">
                                                        <label for="item_name" class="form-label">Item Name</label>
                                                        <input type="text" class="form-control" id="item_name" name="item_name" required>
                                                    </div>
                                                    <div class="col-md-3 col-sm-3">
                                                        <label for="unit" class="form-label">Product Unit</label>
                                                        <select class="form-control" name="unit" required>
                                                            <option selected disabled></option>
                                                            <option value="M">Meter</option>
                                                            <option value="PC">Piece</option>
                                                            <option value="KG">Kilo</option>
                                                            <option value="L">Liter</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 col-sm-3">
                                                        <label for="acron" class="form-label">Acronym</label>
                                                        <input type="text" class="form-control" name="acron" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                <label for="actual_price" class="form-label">Actual Price</label>
                                                <input type="number" class="form-control" id="actual_price" name="actual_price" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                <label for="retail_price" class="form-label">Retail Price</label>
                                                <input type="number" class="form-control" id="retail_price" name="retail_price" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="product_stock" class="form-label">Product Stock</label>
                                                <input type="number" class="form-control" id="product_stock" name="product_stock" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="product_stock" class="form-label">Restock Warning</label>
                                                <input type="number" class="form-control" id="restock" name="restock" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="item_category" class="form-label">Item Category</label>
                                                <select class="form-select" id="item_category" name="item_category" required>
                                                    <option value="">Select an item category...</option>
                                                    <?php
                                                        $q = "SELECT * FROM categories";
                                                        $qq = mysqli_query($conn, $q);

                                                        foreach($qq as $r){
                                                            $catname = $r['category_name'];
                                                    ?>
                                                    <option value="<?php echo $catname ?>"><?php echo $catname ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="item_specification" class="form-label">Item Details</label>
                                                <textarea class="form-control" id="item_specification" placeholder="Input eg. color, brand, size..." name="item_specification" rows="2" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-secondary2" name="add-product">Add Product</button>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </form> 
                        </div>
                    </div>
                </div>
                <div class="table shadow-sm table-inventory bg-white table-responsive-sm">
                    <table class="table table-responsive">
                        <thead class="table-info">
                            <tr>
                                <th></th>
                                <th>Item Name</th>
                                <th>Actual Price</th>
                                <th>Retail Price</th>
                                <th>Product Stock</th>
                                <th>Item Category</th>
                                <th>Date Added</th>
                                <th>Item Description</th>
                                <th>Item Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if (isset($_GET['search_query'])) {
                                    $searchkey = $_GET['search_query'];

                                    $query = "SELECT * FROM products 
                                            WHERE (item_name LIKE ? OR item_details LIKE ? OR item_category LIKE ?)
                                            AND item_status = 'A'";
                                    $stmt = mysqli_prepare($conn, $query);
                                    $searchkeyWithWildcards = '%' . $searchkey . '%';
                                    mysqli_stmt_bind_param($stmt, "sss", $searchkeyWithWildcards, $searchkeyWithWildcards, $searchkeyWithWildcards);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                
                                }  elseif (isset($_GET['inactive'])) {
                                    $query = "SELECT * FROM products WHERE item_status = 'I'";
                                    $result = mysqli_query($conn, $query);
                                }elseif (isset($_POST['navigate'])) {
                                    $id = $_POST['product_id'];
                                    $query = "SELECT * FROM products WHERE item_status = 'A' AND product_id = '$id'";
                                    $result = mysqli_query($conn, $query);
                                }elseif (isset($_GET['sortBy'])) {
                                    $sort = $_GET['sortBy'];
                                    $query = "SELECT * FROM products WHERE item_category = '$sort' AND item_status = 'A'";
                                    $result = mysqli_query($conn, $query);
                                } else {
                                    $query = "SELECT * FROM products WHERE item_status = 'A'";
                                    $result = mysqli_query($conn, $query);
                                }
                                foreach ($result as $items => $row) {
                            ?>
                            <tr class="<?php if($row['product_stock'] <= $row['warning_stock'] ){ echo 'table-danger';}else{} ?>">
                                <td><?php echo $row['acronym']; ?></td>
                                <td><?php echo $row['item_name']; ?></td>
                                <td><?php echo CURRENCY . number_format($row['actual_price'],2); ?></td>
                                <td><?php echo CURRENCY . number_format($row['retail_price'],2); ?></td>
                                <td><?php echo $row['product_stock'] .' '. $row['product_unit']; ?></td>
                                <td><?php echo $row['item_category']; ?></td>
                                <td><?php echo $row['item_details']; ?></td>
                                <td><?php echo $row['date_added']; ?></td>
                                <td><?php echo $row['item_status']; ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#product<?php echo $row['product_id']; ?>"><i class='bx bxs-edit-alt' ></i>Edit</button>
                                        <div class="modal fade" id="product<?php echo $row['product_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel"><i class='bx bxs-edit-alt' ></i>EDIT PRODUCT</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="">
                                                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                                            <div class="row">
                                                                <div class="col-md-7">
                                                                    <div class="mb-3">
                                                                        <label for="new_item_name" class="form-label">Product Name</label>
                                                                        <input type="text" class="form-control" name="new_item_name" id="new_item_name" value="<?php echo $row['item_name']; ?>" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <label for="acron" class="form-label">Acronym</label>
                                                                    <input type="text" class="form-control" name="acron" value="<?php echo $row['acronym'] ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="new_actual_price" class="form-label">Actual Price</label>
                                                                        <input type="number" class="form-control" name="new_actual_price" id="new_actual_price" value="<?php echo $row['actual_price']; ?>" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="new_retail_price" class="form-label">Retail Price</label>
                                                                        <input type="number" class="form-control" name="new_retail_price" id="new_retail_price" value="<?php echo $row['retail_price']; ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label for="new_product_stock" class="form-label">Product Stock</label>
                                                                        <input type="number" class="form-control" name="new_product_stock" id="new_product_stock" value="<?php echo $row['product_stock']; ?>" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="unit" class="form-label">Product Unit</label>
                                                                    <select class="form-control" name="unit" required>
                                                                        <option selected disabled></option>
                                                                        <option value="M">Meter</option>
                                                                        <option value="PC">Piece</option>
                                                                        <option value="KG">Kilo</option>
                                                                        <option value="L">Liter</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="new_item_details" class="form-label">Product Details</label>
                                                                <textarea class="form-control" id="new_item_details" name="new_item_details" rows="2" required><?php echo $row['item_details']; ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="new_item_status" class="form-label">Product Status</label>
                                                                <select class="form-control" id="payment_method" name="new_item_status" required>
                                                                    <?php if($row['item_status'] == 'A') { ?>
                                                                        <option value="A" selected>Active</option>
                                                                        <option value="I">Inactive</option>
                                                                    <?php }elseif($row['item_status'] == 'I'){ ?>
                                                                        <option value="A">Active</option>
                                                                        <option value="I" selected>Inactive</option>
                                                                        <?php } else {?>
                                                                            <option value="A">Active</option>
                                                                            <option value="I">Inactive</option>
                                                                        <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="d-flex justify-content-center">
                                                                <button type="submit" class="btn btn-primary" name="submit_edit">Save Changes</button>
                                                                <button type="submit" class="btn btn-float" name="submit_delete">Delete Item</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
        <div class="row mt-3">
            <div class="col-md-12">
                <button class="btn float-end border bg-primary text-white mb-2" data-bs-toggle="modal" data-bs-target="#cat" name="add_cat">ADD CATEGORY<ion-icon name="add-outline"></ion-icon></button>
                <div class="modal fade" id="cat" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="catLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="catLabel">ADD NEW CATEGORY</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                            <div class="mb-3">
                                <label for="item_name" class="form-label">CATEGORY NAME</label>
                                <input type="text" class="form-control" name="cat_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" name="cat" class="btn btn-primary">SUBMIT</button>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="table table-inventory table-responsive table-responsive-s shadowm">
                    <table class="table table-sm table-responsive shadow-sm text-center">
                        <thead class="table-info">
                            <tr>
                                <td>Category Name</td>
                                <td>Number of Products</td>
                                <td>Status</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $qqqq = "SELECT * FROM categories";
                                $qqq = mysqli_query($conn, $qqqq);

                                while($er = mysqli_fetch_assoc($qqq)){
                                    $catname = $er['category_name'];
                            ?>
                                <tr>
                                    <td><?php echo $catname; ?></td>
                                    <td>
                                        <?php 
                                            $req = "SELECT COUNT(*) as countprod, item_name, retail_price, product_stock FROM products WHERE item_category = '$catname'";
                                            $reqres = mysqli_query($conn, $req);
                                            $ress = mysqli_fetch_assoc($reqres);
                                            $cnt = $ress['countprod'];
                                        ?>
                                         <button class="btn" type="button m-0" data-bs-toggle="collapse" data-bs-target="#int_inven<?php echo $er['cat_id'] ?>" aria-expanded="false"><?php echo $cnt ?></button>
                                         <div class="collapse" id="int_inven<?php echo $er['cat_id'] ?>">
                                            <div class="card-body">
                                                <div class="table table-responsive-sm">
                                                    <table class="table table-sm table-info">
                                                        <thead>
                                                            <tr>
                                                                <td>ITEM NAME</td>
                                                                <td>PRICE</td>
                                                                <td>STOCK</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                $itemsInner = "SELECT item_name, retail_price, product_stock FROM products WHERE item_category = '$catname'";
                                                                $itemRes = mysqli_query($conn, $itemsInner);
                                                                
                                                                foreach($itemRes as $item){
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $item['item_name'] ?></td>
                                                                <td><?php echo CURRENCY .number_format($item['retail_price'], 2) ?></td>
                                                                <td><?php echo $item['product_stock'] ?></td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo $er['cat_status']; ?>
                                    </td>
                                    <td>
                                        <form action="" method="POST">
                                            <input type="hidden" name="cat_id" value="<?php echo $er['cat_id'] ?>">
                                            <button name="delete_cat" type="submit" class="btn btn-outline-danger btn-sm"><ion-icon name="trash-outline"></ion-icon></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    // Enable Bootstrap dropdown for the category-dropdown
    var categoryDropdown = document.querySelector('.category-dropdown');
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
<footer>
<?php require_once './includes/footer.php'; ?>
</footer>
</html>