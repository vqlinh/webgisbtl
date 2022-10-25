

<?php
ob_start(); // loi cua header()
include("./config/constants.php");



if (isset($_POST['submit'])) {
    // xu li up file



    $last_name  = $_POST['lastName'];
    $email      = $_POST['email'];
    $pass1      = $_POST['pass1'];
    $pass2      = $_POST['pass2'];
    if ($pass1 != $pass2) {
        $value = 'failed pass';
        header("Location:register.php?reply=$value");
    } else {
        $pass_hash = password_hash($pass1, PASSWORD_DEFAULT);
        $sql_2 = "INSERT INTO users(first_name, last_name, email, password,code,avatar) 
        VALUES ('$first_name','$last_name','$email','$pass_hash','$code','$image_name')";
        $result_2 = mysqli_query($conn, $sql_2);
        
    }
}

?>