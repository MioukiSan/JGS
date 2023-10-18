<?php 
    require_once './includes/db_conn.php';
    require_once './includes/restriction.php';
    $user_id = $_SESSION['user_id'];
    
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

    if(isset($_POST['Save'])){
        $up_user = $_POST['user'];
        $up_pass = $_POST['pass'];
        $up_type = $_POST['user_type'];

        $private_key = gen_private_key(16);
        $enc_pass = encrypt_password($up_pass, $private_key);

        $up = "UPDATE users SET username = ?, user_pass = ?, hashed_pass = ?, user_type = ?, private_key = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $up);

            if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssi", $up_user, $up_pass, $enc_pass, $up_type, $private_key, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "User information updated successfully!.Logging OUT!";
                header('location: logout.php');
            } else {
                echo "Error updating user information: " . mysqli_error($conn);
            }
        }
    }

    if(isset($_POST['add_account'])){
        $username = $_POST['n_username'];
        $pass = $_POST['n_pass'];
        $usertype = $_POST['n_usertype'];
        
        $private_key = gen_private_key(16);
        $enc_pass = encrypt_password($pass, $private_key);
        
        $sql = "INSERT INTO users (username, user_pass,  hashed_pass, user_type, private_key) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $username, $pass, $enc_pass, $usertype, $private_key);
            if (mysqli_stmt_execute($stmt)) {
                echo "USER ADDED";
            } else {
                echo "Error updating user information: " . mysqli_error($conn);
            }
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <?php include_once './includes/head.php'; ?> 
</head>
<body class="bg-light pad">
    <?php include_once './includes/side_navbar.php'; ?>
    <div class="container con-settings">
        <div class="row mt-3">
            <div class="col-md-12 col-sm-12 p-5 bg-white shadow-sm border">
                <h3 class="border" style="background-color: #1C387C; color: white;">ACCOUNT INFORMATION</h3>
                <?php 
                    $user_id = mysqli_real_escape_string($conn, $user_id);

                    $query = "SELECT * FROM users WHERE user_id = '$user_id'";
                    $result_data = mysqli_query($conn, $query);
                    
                    if ($result_data && mysqli_num_rows($result_data) > 0) {
                        $row = mysqli_fetch_assoc($result_data);
                    }
                ?>
                <?php if(!isset($_POST['edit_profile'])): ?>
                    <form method="POST">
                        <div class="mb-3">
                        <button type="submit" class="btn float-end" name="edit_profile">Edit</button>
                            <label for="username" class="form-label">USERNAME</label>
                            <input type="readonly" name="user" class="form-control" id="username" aria-describedby="emailHelp" placeholder="<?php echo $row['username']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="user_type" class="form-label">USERTYPE</label>
                            <input type="readonly" class="form-control" id="user_type" name="user_type" placeholder="<?php echo $row['user_type']; ?>" readonly>
                        </div>
                    </form>
                <?php elseif(isset($_POST['edit_profile'])):?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">USERNAME</label>
                            <input type="text" name="user" class="form-control" id="username" aria-describedby="emailHelp"  value="<?php echo $row['username']; ?>" >
                        </div>
                        <div class="mb-3">
                            <label for="user_type" class="form-label">USERTYPE</label>
                            <input type="text" class="form-control" id="user_type" name="user_type"  value="<?php echo $row['user_type']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="pass" class="form-label">PASSWORD</label>
                            <input type="password" class="form-control" id="pass" name="pass"  value="<?php echo $row['user_pass']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_pass" class="form-label">CONFIRM PASSWORD</label>
                            <input type="password" class="form-control" id="confirm_pass" name="confirm_pass">
                            <span id="passwordMatchMessage"></span>
                        </div>
                        <div class="mb-3 text-center">
                            <a type="button" href="settings.php" class="btn btn-float" name="cancel">Cancel</a>
                            <button type="submit" class="btn btn-secondary1" name="Save">Save</button>
                        </div>
                    </form>
                <?php endif; ?>
                    <button class="btn float-end" type="button" id="collapse" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="true">
                        ADD NEW ACCOUNT<ion-icon name="person-add-outline"></ion-icon>
                    </button>
                    <br><div class="collapse" id="collapseForm">
                        <form method="POST">
                            <div class="mb-1">
                                <label for="n_username" class="form-label">USERNAME</label>
                                <input type="text" class="form-control" name="n_username" aria-describedby="emailHelp">
                            </div>
                            <div class="row mb-1">
                                <div class="col-md-6 col-sm-6">
                                    <label for="n_pass" class="form-label">PASSWORD</label>
                                    <input type="password" class="form-control" id="pass" name="n_pass">
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <label for="confirm_pass" class="form-label">CONFIRM PASSWORD</label>
                                    <input type="password" class="form-control" id="confirm_pass" name="n_pass">
                                </div>
                                <span id="passwordMatchMessage"></span>
                            </div>
                            <div class="mb-1">
                                <label for="usertype" class="form-label">USERTYPE</label>
                                <select class="form-select" id="usertype" name="n_usertype">
                                    <option value="admin">Admin</option>
                                    <option value="cashier">Cashier</option>
                                </select>
                            </div>
                            <div class="mb-3 text-center pt-3">
                                <a type="button" href="settings.php" class="btn btn-float" name="cancel">Cancel</a>
                                <button type="submit" class="btn btn-secondary1" name="add_account">ADD ACCOUNT</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="container">
        <div class="row mt-3">
            <div class="col-md-12">
                <h2 class="text-center thead-bg">ACCOUNTS</h2>
                <div class="table">
                    <table class="table table-responsive">
                        <thead class="text-center">
                            <tr>
                                <td>ID</td>
                                <td>USERNAME</td>
                                <td>USERTYPE</td>
                                <td>STATUS</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sqle = "SELECT * FROM users WHERE user_type != 'Admin'";
                                $res = mysqli_query($conn, $sqle);
                                foreach($res as $ed){ 
                            ?>
                            <tr>
                                <td><?php echo $ed['user_id'] ?></td>
                                <td><?php echo $ed['username'] ?></td>
                                <td><?php echo $ed['user_type'] ?></td>
                                <td><?php echo $ed['online_offline'] ?></td>
                                <td>
                                    <button type="submit" class="btn btn-sm" name="edit" data-bs-toggle="modal" data-bs-target="#<?php echo $ed['user_id'] ?>"><ion-icon name="create-outline"></ion-icon></button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> -->
</body>
<script>
    // Get references to the password and confirm password fields
    const passwordField = document.getElementById("pass");
    const confirmPasswordField = document.getElementById("confirm_pass");
    const passwordMatchMessage = document.getElementById("passwordMatchMessage");

    // Function to check if the passwords match
    function checkPasswordMatch() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;

        if (password === confirmPassword) {
            passwordMatchMessage.innerHTML = "Password Match";
            passwordMatchMessage.style.color = "green";
        } else {
            passwordMatchMessage.innerHTML = "Password does not match";
            passwordMatchMessage.style.color = "red";
        }
    }

    passwordField.addEventListener("input", checkPasswordMatch);
    confirmPasswordField.addEventListener("input", checkPasswordMatch);
</script>
</html>