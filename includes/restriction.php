<?php 
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        $check_user_query = "SELECT * FROM users WHERE user_id = ?";
        $result = query($conn, $check_user_query, [$user_id]);
        if (count($result) > 0) {
        } else {
            header("Location: ./includes/access_denied.php"); 
            exit();
        }
    } else {
        header("Location: ./includes/access_denied.php"); 
        exit();
    }
    ?>