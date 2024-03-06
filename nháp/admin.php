<?php
    session_start();
    
    if (!isset($_SESSION['email'])) {//nếu session này k tồn tại
        header('location:login.php');//thì sẽ đẩy ng dùng qua trang login
    }
?>

<h1>trang Admin</h1>