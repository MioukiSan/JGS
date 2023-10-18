<?php 
    require_once './includes/db_conn.php';
    
    if (isset($_POST['login'])) {
        $username = $_POST['user'];
        $password = $_POST['pass'];
        
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
    
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $enc_password = encrypt_password($password, $row['private_key']); 
    
            if ($enc_password === $row['hashed_pass']) {
                // Update the online_offline status to 'online'
                $updateStatusSql = "UPDATE users SET online_offline = 'online' WHERE user_id = {$row['user_id']}";
                mysqli_query($conn, $updateStatusSql);
    
                $_SESSION['user_id'] = $row['user_id'];
                
                $login_suc = 'Hi ' . $row['user_type'];
                header("Location: ./dashboard.php?success=login&msg=$login_suc");
                exit();
            } else {
                // Passwords do not match
                $login_error = 'Invalid email or password';
                header("Location: ./index.php?error=login&msg=$login_error");
                exit();
            }
        } else {
            // User not found
            $login_error = 'Invalid email or password';
            header("Location: ./index.php?error=login&msg=$login_error");
            exit();
        }
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JGS POS SYSTEM</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <?php include_once './includes/head.php'; ?>
    <style>
        .box {
            width: 23em;
            height: 26em;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-left: 2em;
        }
        .card {
            width: 50em;
            height: 26em;
            margin-left: 22.5em;
            padding: .5em;
        }
        @media (max-width: 768px) {
            .card {
                width: 22em; 
                height: auto;
                max-width: 100%;
                margin-left: 0; 
            }
            .img-in {
                display: none;
            }
        }
        @media (max-width: 813.33px) {
            .card {
                margin-left: 0; 
                height: auto;
                max-width: 100%;
                /* width: 24em;  */
            }
        }
        .box p {
            font-size: 5.5em;
            font-weight: bolder;
            color: white;
            background-color: #1C387C;
            position: relative;
            text-align: center;
        }

        .btn {
            color: white;
            background-color: #1C387C;
            width: 10em;
            margin-top: 1.5em;
        }

        .btn:hover {
            color: #1C387C;
            background-color: white;
            border-color: #1C387C;
        }

        .input-group {
            width: 80%;
            margin: 1em auto;
        }
        img {
            width: 20em;
            margin-top: 4em;
        }
        
    </style>
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container-fluid">
        <!-- <div class="row">
            <div class="col">
                <div class="box">
                    <div class="logo-name">
                        <p>LOGIN</p>
                    </div>
                    <form action="" method="POST">
                        <div class="input-group mb-2 pt-4">
                            <span class="input-group-text" id="username"><ion-icon name="person-sharp"></ion-icon></span>
                            <input type="text" class="form-control" name="user" placeholder="Username" aria-label="Username" aria-describedby="username" required>
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="password"><ion-icon name="lock-closed"></ion-icon></span>
                            <input type="password" class="form-control" name="pass" placeholder="Password" aria-label="Password" aria-describedby="password" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="login" class="btn"><i class='bx bx-log-in' style='color:#f5f5f5'  ></i>  LOG IN</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <form action="" method="POST">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7 col-sm-7">
                                <h1 class="text-center" style="color: #1C387C; padding: .1em; margin-top: 1em;">LOGIN</h1>
                                <div class="input-group mb-2 pt-4">
                                    <span class="input-group-text" id="username"><ion-icon name="person-sharp"></ion-icon></span>
                                    <input type="text" class="form-control" name="user" placeholder="Username" aria-label="Username" aria-describedby="username" required>
                                </div>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="password"><ion-icon name="lock-closed"></ion-icon></span>
                                    <input type="password" class="form-control" name="pass" placeholder="Password" aria-label="Password" aria-describedby="password" required>
                                </div>
                                <div class="text-center">
                                    <button type="submit" name="login" class="btn"><i class='bx bx-log-in' style='color:#f5f5f5;'></i>  LOG IN</button>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-5 img-in">
                                <img src="./images//jgs-index.png" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
