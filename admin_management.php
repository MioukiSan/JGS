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

    $employ_id = 0;

    if(isset($_POST['edit_id'])){
        $employ_id = $_POST['employ_id'];

        $edit_sql = "SELECT * FROM employee_management WHERE employee_id = $employ_id";
        $edit_res = mysqli_query($conn, $edit_sql);
        if (!$edit_res) {
            echo "Error: " . mysqli_error($conn);
        } else {
            while ($row = mysqli_fetch_assoc($edit_res)) {
                $employ_id = $row['employee_id'];
                $fullname = $row['fullname'];
                $address = $row['address'];
                $postalcode = $row['postal_code'];
                $age = $row['age'];
                $gender = $row['gender'];
                $job_title = $row['job_title'];
                $con_num = $row['contact_num'];
                $base_salary = $row['base_salary'];
            }
        }
    }

    if (isset($_POST["add"])) {
        // Retrieve data from the form
        $employeeName = $_POST["employee_name"];
        $employeeAddress = $_POST["employee_address"];
        $postalCode = $_POST["postal_code"];
        $gender = $_POST["gender"];
        $age = $_POST["age"];
        $jobTitle = $_POST["job_title"];
        $contactNumber = $_POST["con_num"];
        $baseSalary = $_POST["base_salary"];

        // Prepare and execute the SQL query to insert data into the table
        $sql = "INSERT INTO employee_management (fullname, address, postal_code, gender, age, job_title, contact_num, base_salary) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $employeeName, $employeeAddress, $postalCode, $gender, $age, $jobTitle, $contactNumber, $baseSalary);

        if ($stmt->execute()) {
            // Redirect to the current page (refresh)
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

    }
    if (isset($_POST["update"])) {
        // Handle the update action here
        $employeeIdToUpdate = $_POST["id-employ"];
        $newEmployeeName = $_POST["employee_name"];
        $newEmployeeAddress = $_POST["employee_address"];
        $newPostalCode = $_POST["postal_code"];
        $newGender = $_POST["gender"];
        $newAge = $_POST["age"];
        $newJobTitle = $_POST["job_title"];
        $newContactNumber = $_POST["con_num"];
        $newBaseSalary = $_POST["base_salary"];

        $updateQuery = "UPDATE employee_management SET
            fullname = '$newEmployeeName',
            address = '$newEmployeeAddress',
            postal_code = '$newPostalCode',
            gender = '$newGender',
            age = '$newAge',
            job_title = '$newJobTitle',
            contact_num = '$newContactNumber',
            base_salary = '$newBaseSalary'
            WHERE employee_id = $employeeIdToUpdate";

        if (mysqli_query($conn, $updateQuery)) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    } elseif (isset($_POST["delete"])) {
        $employeeIdToDelete = $_POST["id-employ"];
    
        // Perform the update query to set employee_status to 'inactive'
        $updateStatusQuery = "UPDATE employee_management SET employee_status = 'inactive' WHERE employee_id = $employeeIdToDelete";
    
        if (mysqli_query($conn, $updateStatusQuery)) {
            echo "Employee marked as inactive successfully";
        } else {
            echo "Error marking employee as inactive: " . mysqli_error($conn);
        }
    }

    if(isset($_POST['status'])) {
        $employee_id = $_POST['employee_id'];
        $status = $_POST['status'];

        $insertQuery = "INSERT INTO attendance (employee_id, attendance_status) VALUES ('$employee_id', '$status')";
    
        if (mysqli_query($conn, $insertQuery)) {
            header("Location: admin_management.php?message=Attendance marked successfully");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
            header("Location: admin_management.php?error=Failed to mark attendance");
            exit();
        }
    }
    if (isset($_POST['status_change'])) {
        $employee_id = $_POST['employee_id'];
        $datee = $_POST['date'];
        $n_status = $_POST['n_status'];
        if($n_status == 'Absent'){
            $updateQuery = "UPDATE attendance SET attendance_status = 'Present' WHERE employee_id = '$employee_id' AND date = '$datee'";
        }else{
            $updateQuery = "UPDATE attendance SET attendance_status = 'Absent', salary = NULL WHERE employee_id = '$employee_id' AND date = '$datee'";
        }
        if (mysqli_query($conn, $updateQuery)) {
            // Redirect to a success page with a message
            header("Location: admin_management.php?message=Attendance updated successfully");
            exit();
        } else {
            // Handle the error and redirect with an error message
            $error_message = "Error: " . mysqli_error($conn);
            header("Location: admin_management.php?error=" . urlencode($error_message));
            exit();
        }
    }
    if (isset($_POST['halfday']) || isset($_POST['whole'])) {
        // Get data from the POST request
        $date = $_POST['date'];
        $id = $_POST['employee_id'];
        if (isset($_POST['halfday'])) {
            // Query to fetch base salary of the employee
            $sql_sl = "SELECT base_salary FROM employee_management WHERE employee_id = '$id'";
            $res = mysqli_query($conn, $sql_sl);
    
            if ($res) {
                $employeeData = mysqli_fetch_assoc($res);
                $base = $employeeData['base_salary'];
    
                // Assuming 'half' represents half of the base salary
                $half_base = $base / 2;
    
                // Construct the SQL query to update the salary for a half-day
                $sqlUpdate = "UPDATE attendance SET salary = '$half_base' WHERE employee_id = '$id' AND date = '$date'";
    
                // Perform the SQL update for half-day
                $result = mysqli_query($conn, $sqlUpdate);
    
                if ($result) {
                    // Update for half-day was successful
                    echo "Half-day salary updated successfully!";
                } else {
                    // Update for half-day failed
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                // Query to fetch base salary failed
                echo "Error: " . mysqli_error($conn);
            }
        }
        if (isset($_POST['whole'])) {
            // Query to fetch base salary of the employee
            $sql_sl = "SELECT base_salary FROM employee_management WHERE employee_id = '$id'";
            $res = mysqli_query($conn, $sql_sl);
    
            if ($res) {
                $employeeData = mysqli_fetch_assoc($res);
                $base = $employeeData['base_salary'];
    
                // Construct the SQL query to update salary in the attendance table for a full day
                $sqlUpdate = "UPDATE attendance SET salary = '$base' WHERE employee_id = '$id' AND date = '$date'";
    
                // Perform the SQL update for a full day
                $result = mysqli_query($conn, $sqlUpdate);
    
                if ($result) {
                    // Update for a full day was successful
                    echo "Salary updated successfully!";
                } else {
                    // Update for a full day failed
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                // Query to fetch base salary failed
                echo "Error: " . mysqli_error($conn);
            }
        }
    }
    if (isset($_POST['salary_submit'])) {
        $employee_id = $_POST['employee_id'];
        $date = $_POST['date'];
        $custom_salary = $_POST['custom_salary'];
        if (!is_numeric($custom_salary)) {
            echo "Please enter a valid number for the custom salary.";
            exit; // Stop further execution if the input is invalid
        }
        $sqlUpdate = "UPDATE attendance SET salary = '$custom_salary' WHERE employee_id = '$employee_id' AND date = '$date'";
    
        $result = mysqli_query($conn, $sqlUpdate);
    
        if ($result) {
            echo "Custom salary updated successfully!";
        } else {
            echo "Error updating custom salary: " . mysqli_error($conn);
        }
    }    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <?php include_once './includes/head.php'; ?>  
</head>
<body class="bg-light" style="padding-left: 85px;">
    <?php include_once './includes/side_navbar.php'; ?> 
    <div class="container-fluid">
        <div class="row mt-4 border shadow-sm">
            <?php if($employ_id == 0) :?>
            <div class="col-md-6 col-sm-6 bg-white border">
            <form action="" method="POST">
                <div class="mb-2 mt-2 row">
                    <label for="employee_name" class="col-sm-4 col-form-label">Employee Name</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="employee_name" name="employee_name">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="employee_address" class="col-sm-4 col-form-label">Address</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="employee_address" name="employee_address">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="postal_code" class="col-sm-4 col-form-label">Postal Code</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" name="postal_code" id="postal_code">
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <label class="col-sm-5">Gender</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender" value="Male">
                        <label class="form-check-label" for="gender_male">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender" value="Female">
                        <label class="form-check-label" for="gender_female">Female</label>
                    </div>
                </div>
            </div>
            <div class="col-md-5 col-sm-5 bg-white">
                <div class="mb-2 row mt-2">
                    <label for="age" class="col-sm-4 col-form-label">Age</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="age" name="age">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="job_title" class="col-sm-4 col-form-label">Job Title</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="job_title" name="job_title">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="con_num" class="col-sm-4 col-form-label">Contact Number</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="con_num" name="con_num">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="base_salary" class="col-sm-4 col-form-label">Base Salary</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="base_salary" name="base_salary">
                    </div>
                </div>
            </div>
            <div class="col-md-1 col-sm-1 border bg-white text-center">
                <div class="mb-2 mt-3">
                    <button type="submit" name="add" class="btn btn-outline-primary">Add</button>
                </div>
                <div class="mb-2">
                    <button type="submit" name="update" class="btn btn-outline-success">Update</button>
                </div>
                <div class="mb-1">
                    <button type="submit" name="delete" class="btn btn-outline-danger">Delete</button>
                </div>
                </form>
                <div class="mb-2">
                    <form action="admin_management.php" method="POST">
                        <button type="submit" name="reset" class="btn btn-outline-secondary">Reset</button>
                    </form>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="col-md-6 col-sm-6 bg-white">
                <form action="" method="POST">
                    <input type="hidden" name="id-employ" value="<?php echo $employ_id; ?>">
                <div class="mb-2 mt-2 row">
                        <label for="employee_name" class="col-sm-4 col-form-label">Employee Name</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" id="employee_name" name="employee_name" value="<?php echo $fullname; ?>">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="employee_address" class="col-sm-4 col-form-label">Address</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="employee_address" name="employee_address" value="<?php echo $address; ?>">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="postal_code" class="col-sm-4 col-form-label">Postal Code</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" name="postal_code" id="postal_code" value="<?php echo $postalcode; ?>">
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <label class="col-sm-5">Gender</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender_male" value="Male" <?php echo ($gender == "Male") ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="gender_male">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender_female" value="Female" <?php echo ($gender == "Female") ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="gender_female">Female</label>
                    </div>
                </div>
            </div>
            <div class="col-md-5 col-sm-5 bg-white">
                <div class="mb-2 row mt-2">
                    <label for="age" class="col-sm-4 col-form-label">Age</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="age" name="age" value="<?php echo $age; ?>">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="job_title" class="col-sm-4 col-form-label">Job Title</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="job_title" name="job_title" value="<?php echo $job_title; ?>">
                    </div>
                </div>
                <div class="mb-2 row mt-2">
                    <label for="con_num" class="col-sm-4 col-form-label">Contact Number</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="con_num" name="con_num" value="<?php echo $con_num; ?>">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="base_salary" class="col-sm-4 col-form-label">Base Salary</label>
                    <div class="col-sm-8">
                    <input type="number" class="form-control form-control-sm" id="base_salary" name="base_salary" value="<?php echo $base_salary; ?>">
                    </div>
                </div>
            </div>
            <div class="col-mb-1 col-sm-1 border bg-white text-center">
                <div class="mb-2 mt-3">
                    <button type="button" name="add" class="btn btn-outline-primary">Add</button>
                </div>
                <div class="mb-2">
                    <button type="submit" name="update" class="btn btn-outline-success">Update</button>
                </div>
                <div class="mb-1">
                    <button type="submit" name="delete" class="btn btn-outline-danger">Delete</button>
                </div>
            </form>
                <div class="mb-2">
                    <form action="admin_management.php" method="POST">
                        <button type="submit" name="reset" class="btn btn-outline-secondary">Reset</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif;?>
        <div class="row mt-4 bg-white">
            <table class="table-responsive text-center">
                <thead class="border thead-bg">
                    <tr>
                        <th scope="col">Full Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Base Salary</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Postal Code</th>
                        <th scope="col">Job Title</th>
                        <th scope="col">Contact Number</th>
                        <th scope="col">Attendace(TODAY)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $employee_sql = "SELECT * FROM employee_management WHERE employee_status = 'Active'";
                $result_employ = mysqli_query($conn, $employee_sql);
                if (!$result_employ) {
                    echo "Error: " . mysqli_error($conn);
                } else {
                    while ($row1 = mysqli_fetch_assoc($result_employ)) {
                        $employee_ids = $row1['employee_id'];
                ?>
                    <tr>
                        <td><?php echo $row1['fullname']; ?></td>
                        <td><?php echo $row1['address']; ?></td>
                        <td><?php echo $row1['base_salary']; ?></td>
                        <td><?php echo $row1['gender']; ?></td>
                        <td><?php echo $row1['postal_code']; ?></td>
                        <td><?php echo $row1['job_title']; ?></td>
                        <td><?php echo $row1['contact_num']; ?></td>
                        <td>
                        <?php 
                            $rd = date('Y-m-d');

                            $att = "SELECT * FROM attendance WHERE employee_id = '$employee_ids' AND date = '$rd'";
                            $result = mysqli_query($conn, $att); 

                            if (mysqli_num_rows($result) == 0):
                            ?>
                                <form method='post' action=''>
                                    <input type='hidden' name='employee_id' value='<?php echo $employee_ids; ?>'>
                                    <input type='hidden' name='date' value='<?php echo $rd; ?>'>
                                    <input type='submit' class='btn btn-danger' name='status' value='Absent'>
                                    <input type='submit' class='btn btn-success' name='status' value='Present'>
                                </form>
                            <?php else:
                                $attendance = mysqli_fetch_assoc($result);
                                $attendanceStatus = $attendance['attendance_status'];
                                $salary = $attendance['salary'];
                                $color = ($attendanceStatus == 'Absent') ? 'red' : 'green';

                                if($salary === NULL && $attendanceStatus !== 'Absent'){
                         ?> 
                        <form action="" method="POST">
                            <input type='hidden' name='date' value='<?php echo $rd; ?>'>
                            <input type='hidden' name='employee_id' value='<?php echo $employee_ids; ?>'>
                            <button type="submit" name="halfday" class="btn btn-outline-warning">Half Day</button>
                            <button type="submit" name="whole" class="btn btn-outline-success">Whole Day</button>
                        </form>
                        <?php }else{ ?>
                            <p>Today's Salary:  <b><i><?php echo CURRENCY . number_format($attendance['salary'], 2) ?></i></b></p>
                        <?php } if($attendanceStatus === 'Present'){ ?>
                        <button type="button" name="customsalary" class="btn btn-outline-danger mt-3" data-bs-toggle="modal" data-bs-target="#<?php echo $employee_ids; ?>customsalary">Custom</button>
                        <div class="modal fade" id="<?php echo $employee_ids; ?>customsalary" tabindex="-1" aria-labelledby="custom_salary" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="custom-salary">Custom Salary</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" method="POST">
                                            <input type='hidden' name='employee_id' value='<?php echo $employee_ids; ?>'>
                                            <input type='hidden' name='date' value='<?php echo $rd; ?>'>
                                            <input type="text" name="custom_salary" class="form-control form-control-sm mb-2">
                                            <button type="submit" name="salary_submit" class="btn btn-secondary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }else{}?>
                        <button type='button' class='btn mb-2' name='status' data-bs-toggle="modal" data-bs-target="#<?php echo $employee_ids; ?>Modal">
                            Change Status
                        </button>
                        <div class="modal fade" id="<?php echo $employee_ids; ?>Modal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="passwordModalLabel">Enter Password to Change Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php 
                                            $user = $_SESSION['user_id'];
                                            $pass = "SELECT user_pass FROM users WHERE user_id = '$user'";
                                            $pas = mysqli_query($conn, $pass);
                                            $ps = mysqli_fetch_assoc($pas);
                                            $passw = $ps['user_pass'];
                                        ?>
                                        <div class="mb-3">
                                                <label for="password" class="form-label">Password:</label>
                                                <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </div>
                                    <form action="" method="POST">
                                    <div class="modal-footer">
                                        <input type='hidden' name='employee_id' value='<?php echo $employee_ids; ?>'>
                                        <input type="hidden" name="n_status" value="<?php echo $attendanceStatus ?>">
                                        <input type='hidden' name='date' value='<?php echo $rd; ?>'>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="status_change">Change Status</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                                <i class='bx bxs-circle' style='color: <?php echo $color; ?>;'></i> <?php echo $attendanceStatus; ?>
                        <?php endif; ?>
                        </td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="employ_id" value="<?php echo $employee_ids; ?>">
                                <button type="submit" class="btn btn-float-end" name="edit_id"><i class='bx bxs-edit-alt bx-sm' ></i></button>
                            </form>
                        </td>
                    </tr>
                <?php }  } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var passwordInput = document.getElementById('password');
        var changeStatusBtn = document.querySelector('[name="status_change"]');
        var storedPassword = '<?php echo $passw; ?>'; // Get the stored password from PHP

        // Function to enable or disable the button based on password correctness
        function updateButtonState() {
            changeStatusBtn.disabled = (passwordInput.value !== storedPassword);
        }

        // Enable or disable the button on page load
        updateButtonState();

        // Enable or disable the button when the password input changes
        passwordInput.addEventListener('input', function () {
            updateButtonState();
        });
    });
</script>

</html>