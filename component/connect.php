<?php 
    $db_name = 'mysql:host=localhost;dbname=secretbeauty';
    $user_name = 'root';
    $user_password = '';

    //tạo biến tên $conn để lưu trữ đối tượng kết nối cơ sở dữ liệu
    $conn = new PDO($db_name, $user_name, $user_password);
    if ($conn) {
        //echo "not connected";
    }

    // $conn = mysqli_connect('localhost', 'root', '', 'secretbeauty');
    // // if ($conn) {
    // //     echo "not connected";
    // // }
    //Hàm này trả về một chuỗi mã định danh duy nhất bao gồm 20 ký tự ngẫu nhiên
    
    
    function unique_id() {
        $chars = '-*@123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($chars); //gán độ dài chuỗi chars
        $randomString = '';
        for ($i=0; $i<10; $i++){
            $randomString .= $chars[mt_rand(0, $charLength-1)];
        }
        return $randomString;
    }
    $uni = unique_id();

    function sp_id() {
        $chars = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($chars); //gán độ dài chuỗi chars
        $randomString = '';
        for ($i=0; $i<10; $i++){
            $randomString .= $chars[mt_rand(0, $charLength-1)];
        }
        return $randomString;
    }
    $sp = sp_id();


?>