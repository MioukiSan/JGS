<?php
    function generateSalesTransactionCode($conn) {
        $sql = "SELECT sales_transaction_code FROM sales WHERE sales_transaction_code LIKE ? ORDER BY transaction_id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        
        $pattern = 'JGS' . date('dmY') . '%';
        $stmt->bind_param("s", $pattern);
        
        $stmt->execute();
        $result = $stmt->get_result();
      
        if (!$result || $result->num_rows === 0) {
            $unique_part = 1;
        } else {
            $lastTransactionCode = $result->fetch_object();
      
            $lastUniquePart = substr($lastTransactionCode->sales_transaction_code, strlen('JGS') + strlen(date('dmY')) + 1);
      
            // Increment the transaction counter by 1.
            $unique_part = $lastUniquePart + 1;
        }
      
        // Generate the sales transaction code.
        $code = 'JGS' . date('dmY') . '-' . $unique_part;
      
        // Return the sales transaction code.
        return $code;
    }
    

// function generateSalesTransactionCode() {
//     $prefix = 'JGS'; // The first 3 characters
//     $unique_part = mt_rand(100000, 999999); // Generate a random 6-digit number
//     $code = $prefix . $unique_part;
//     return $code;
// }

function gen_private_key($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    $characters_length = strlen($characters);

    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }

    return md5($random_string);
}
function encrypt_password($password, $private_key) {
    $encrypted_password = md5($password . $private_key);
    return $encrypted_password;
}

?>