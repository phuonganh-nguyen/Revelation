<?php
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else{
        $user_id = '';
        header('location:login.php');
    }
    if (isset($_POST['add_sale'])) {
        echo "<pre>";
        print_r($_POST);
        die();
    }
    if (isset($_POST['them'])) {
        $name = $_POST['name'];
        $number = $_POST['number'];
        $flat = $_POST['flat'];
        $phuong = $_POST['phuong'];
        $quan = $_POST['quan'];
        $tinh = $_POST['tinh_tp'];
        $address_type = $_POST['address_type'];

        // $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
        // $verify_cart->execute([$user_id]);

        $insert_shipping = $conn->prepare("INSERT INTO `vanchuyen`(user_id, name, phone, loai_dc, dc_cap1, phuong, quan, tinh)
            VALUES(?,?,?,?,?,?,?,?)");
        $insert_shipping->execute([$user_id, $name, $number, $address_type, $flat, $phuong, $quan, $tinh]);
        header('location:thongtinthanhtoan.php');
    } elseif (isset($_POST['capnhat'])){
        $name = $_POST['name'];
        $number = $_POST['number'];
        $flat = $_POST['flat'];
        $phuong = $_POST['phuong'];
        $quan = $_POST['quan'];
        $tinh = $_POST['tinh_tp'];
        $address_type = $_POST['address_type'];

        // $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
        // $verify_cart->execute([$user_id]);

        $update_shipping = $conn->prepare("UPDATE `vanchuyen` SET name=?, phone=?, loai_dc=?, dc_cap1=?, phuong=?, quan=?, tinh=? WHERE user_id=?");
        $update_shipping->execute([$name, $number, $address_type, $flat, $phuong, $quan, $tinh, $user_id]);
        header('location:thongtinthanhtoan.php');
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Checkout page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <script src="https://esgoo.net/scripts/jquery.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php' ?>
    
    <div class="checkout">
    <div class="heading">
            <h1>Thanh toán</h1>
        </div>
        <div class="container">
            <!-- Responsive Arrow Progress Bar -->
            <div class="arrow-steps clearfix">
                <!-- <div class="step done"> <span> <a href="checkout.php" >Thanh toán</a></span> </div> -->
                <div class="step current"> <span><a href="vanchuyen.php" >Vận chuyển</a></span> </div>
                <div class="step"> <span><a href="thongtinthanhtoan.php" >Thông tin thanh toán</a><span> </div>
            </div>
        </div>
        
        <div class="row">
            <?php 
                $select_shipping = $conn->prepare("SELECT * FROM `vanchuyen` WHERE user_id=?");
                $select_shipping->execute([$user_id]);
                if ($select_shipping->rowCount() > 0){
                    $fetch_shipping = $select_shipping->fetch(PDO::FETCH_ASSOC);
                    $name = $fetch_shipping['name'];
                    $number = $fetch_shipping['phone'];
                    $flat = $fetch_shipping['dc_cap1'];
                    $phuong = $fetch_shipping['phuong'];
                    $quan = $fetch_shipping['quan'];
                    $tinh = $fetch_shipping['tinh'];
                    $address_type = $fetch_shipping['loai_dc'];
                } else {
                    $name = '';
                    $number = '';
                    $flat = '';
                    $phuong = '';
                    $quan = '';
                    $tinh = '';
                    $address_type = '';
                }
            ?>
            <form action="" method="post" class="register" style="margin-bottom: -1rem;">
                <input type="hidden" name="p_id" value="<?= $get_id; ?>">
                
                <!-- <h3>Thông tin vận chuyển</h3> -->
                <div class="flex">
                    <div class="box">
                        <div class="input-field">
                            <p>Họ và tên <span>*</span></p>
                            <input type="text" name="name" required maxlength="50" value="<?php echo $name ?>" placeholder="Nhập họ và tên" class="input">
                        </div>
                        <div class="input-field">
                            <p>Số điện thoại <span>*</span></p>
                            <input type="number" name="number" required maxlength="10" value="<?php echo $number ?>" placeholder="Nhập số điện thoại" class="input">
                        </div>
                        <div class="input-field">
                            <p>Loại địa chỉ <span>*</span></p>
                            <select name="address_type" value="<?php echo $address_type ?>" class="input">
                                <option value="home">Nhà riêng</option>
                                <option value="office">Văn phòng</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Tỉnh/Thành phố <span>*</span></p>
                            <input type="text" name="tinh_tp" required maxlength="50" value="<?php echo $tinh ?>" placeholder="Nhập Tỉnh/Thành phố" class="input">
                           
                        </div>
                        <!-- <div class="input-field">
                            <p>Tỉnh/Thành phố <span>*</span></p>
                            <select class="input" id="tinh" name="tinh_tp" required>
                                <option value="">Chọn Tỉnh/Thành phố</option>
                                <?php
                                $select_tinh = $conn->prepare("SELECT * FROM `tinh`");
                                $select_tinh->execute();
                                while ($row = $select_tinh->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                    <option value="<?= $row['name']?>"><?= $row['name']?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div> -->
                        <div class="form-group">
                            <label for="province">Tỉnh/Thành phố</label>
                            <select id="province" name="province" class="input">
                                <option value="<?php echo $tinh ?>">Chọn một tỉnh</option>
                                <!-- populate options with data from your database or API -->
                                <?php
                                while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                    <option value="<?php echo $row['province_id'] ?>"><?php echo $row['name'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Quận/Huyện <span>*</span></p>
                            <input type="text" name="quan" required maxlength="50" value="<?php echo $quan ?>" placeholder="Nhập Quận/Huyện" class="input">
                            <!-- <select class="input" id="quan" name="quan" placeholder="Nhập quận/huyện" title=" ">
                                <option value="<?php echo $quan ?>"></option>
                            </select>  -->
                        </div>
                        <div class="input-field">
                            <p>Phường/Xã <span>*</span></p>
                            <input type="text" name="phuong" required maxlength="50" value="<?php echo $phuong ?>" placeholder="Nhập Phường/Xã" class="input">
                            <!-- <select class="input" id="phuong" name="phuong" placeholder="Nhập phường/xã" title="Chọn Phường Xã">
                                <option value="<?php echo $phuong ?>"></option>
                            </select> -->
                        </div>
                        
                        <div class="input-field">
                            <p>Tên đường, tòa nhà, số nhà <span>*</span></p>
                            <input type="text" name="flat" required maxlength="50" value="<?php echo $flat ?>" placeholder="Nhập tên đường, tòa nhà, số nhà" class="input">
                        </div>
                        <!-- <div class="input-field">
                            <p>Hình thức vận chuyển <span>*</span></p>
                            <select name="shipping_type" value="<?php echo $shipping_type ?>" class="input">
                                <option value="Giao hàng tiết kiệm">Tiết kiệm (từ 4-5 ngày): 28.700 VNĐ</option>
                                <option value="Giao hàng nhanh">Nhanh (từ 1-2 ngày): 35.200 VNĐ</option>
                                <option value="Giao hàng hỏa tốc">Hỏa tốc (trong 1 ngày): 50.000 VNĐ</option>
                            </select>
                        </div> -->
                    </div>
                </div>
                <!-- <input type="text" name="note" maxlength="500" placeholder="Nhập ghi chú" class="input"> -->
                <?php 
                    if ($name == ''){
                ?>
                    <button type="submit" name="them" class="btn">Thêm</button>
                <?php        
                    } else {
                ?>
                    <button type="submit" name="capnhat" class="btn">Cập nhật</button>
                <?php
                    }
                ?>
                
            </form>
            
        </div>
    </div>
<style type="text/css">
    .css_select_div { text-align: center; }
    .css_select { display: inline-table; width: 25%; padding: 5px; margin: 5px 2%; border: solid 1px #686868; border-radius: 5px; }
</style>


    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>