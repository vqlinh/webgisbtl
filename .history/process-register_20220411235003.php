

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
        $sql = "INSERT INTO users( name, email, password) 
        VALUES ('$last_name','$email','$pass_hash')";
        $result = mysqli_query($conn, $sql);
        if ($result){
            $_SESSION['noti'] = '<p class = "text-success">Đăng kí thành công.</p>';
            header("location:login.php");
        }else{
            $_SESSION['noti'] = '<p class = "text-danger">Lỗi khi kích hoạt tài khoản! Vui lòng thử lại</p>';
                    header("location:" . $siteurl . "login.php");
        }
    }
}
?>