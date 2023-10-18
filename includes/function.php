<?php

function generateSalesTransactionCode() {
    $prefix = 'JGS'; // The first 3 characters
    $unique_part = mt_rand(100000, 999999); // Generate a random 6-digit number
    $code = $prefix . $unique_part;
    return $code;
}

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