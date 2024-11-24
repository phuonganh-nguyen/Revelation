<?php
    include 'connect.php';
 
    setcookie('khach_id', '', time()-1, '/');
    header('location: ../home.php');
?>