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
        $tinh_id = $_POST['tinh']; // Chỉnh sửa từ tinh_tp thành tinh
        $huyen_id = $_POST['huyen']; // Lấy giá trị huyện
        $xa_id = $_POST['xa'];
        $address_type = $_POST['address_type'];

        // Lấy tên tỉnh
        $stmt = $conn->prepare("SELECT name FROM `tinh` WHERE tinh_id = ?");
        $stmt->execute([$tinh_id]);
        $tinh = $stmt->fetchColumn();

        // Lấy tên huyện
        $stmt = $conn->prepare("SELECT name FROM `huyen` WHERE huyen_id = ?");
        $stmt->execute([$huyen_id]);
        $huyen = $stmt->fetchColumn(); // Lấy tên huyện

        // Lấy tên xã
        $stmt = $conn->prepare("SELECT name FROM `xa` WHERE xa_id = ?");
        $stmt->execute([$xa_id]);
        $xa = $stmt->fetchColumn();

        $insert_shipping = $conn->prepare("INSERT INTO `vanchuyen`(user_id, name, phone, loai_dc, dc_cap1, tinh, quan, phuong)
            VALUES(?,?,?,?,?,?,?,?)");
        $insert_shipping->execute([$user_id, $name, $number, $address_type, $flat, $tinh, $huyen, $xa]);
        header('location:thongtinthanhtoan.php');
    } elseif (isset($_POST['capnhat'])){
        $name = $_POST['name'];
        $number = $_POST['number'];
        $flat = $_POST['flat'];
        $tinh_id = $_POST['tinh']; // Chỉnh sửa từ tinh_tp thành tinh
        $huyen_id = $_POST['huyen']; // Lấy giá trị huyện
        $xa_id = $_POST['xa'];
        $address_type = $_POST['address_type'];

        // Lấy tên tỉnh
        $stmt = $conn->prepare("SELECT name FROM `tinh` WHERE tinh_id = ?");
        $stmt->execute([$tinh_id]);
        $tinh = $stmt->fetchColumn();

        // Lấy tên huyện
        $stmt = $conn->prepare("SELECT name FROM `huyen` WHERE huyen_id = ?");
        $stmt->execute([$huyen_id]);
        $huyen = $stmt->fetchColumn(); // Lấy tên huyện

        // Lấy tên xã
        $stmt = $conn->prepare("SELECT name FROM `xa` WHERE xa_id = ?");
        $stmt->execute([$xa_id]);
        $xa = $stmt->fetchColumn();

        $update_shipping = $conn->prepare("UPDATE `vanchuyen` SET name=?, phone=?, loai_dc=?, dc_cap1=?, tinh=?, quan=?, phuong=? WHERE user_id=?");
        $update_shipping->execute([$name, $number, $address_type, $flat, $tinh, $huyen, $xa, $user_id]);
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
            <h1>Thông tin giao hàng</h1>
        </div>
        <!-- <div class="container">
            <div class="arrow-steps clearfix">
                <div class="step current"> <span><a href="vanchuyen.php" >Thông tin giao hàng</a></span> </div>
                <div class="step"> <span><a href="thongtinthanhtoan.php" >Thông tin thanh toán</a><span> </div>
            </div>
        </div> -->
        
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
            <form action="" method="post" class="register" onsubmit="return validateForm()" style="margin-bottom: -1rem;">
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
                            <input type="number" name="number" required maxlength="10" pattern="\d{10}" value="<?php echo $number ?>" placeholder="Nhập số điện thoại" class="input">
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
                            <select id="tinh" name="tinh" class="input">
                                <option value="" disabled selected>Chọn Tỉnh/Thành phố</option>
                                <?php
                                // Lấy danh sách tỉnh từ cơ sở dữ liệu
                                $stmt = $conn->query("SELECT * FROM `tinh`");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . $row['tinh_id'] . '">' . $row['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Quận/Huyện <span>*</span></p>
                            <select id="huyen" name="huyen" class="input" disabled>
                                <option value="" disabled selected>Chọn Quận/Huyện</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Xã/Phường/Thị trấn <span>*</span></p>
                            <select id="xa" name="xa" class="input" disabled>
                                <option value="" disabled selected>Chọn Xã/Phường/Thị trấn</option>
                            </select>
                        </div> 
                        <div class="input-field">
                            <p>Tên đường, tòa nhà, số nhà <span>*</span></p>
                            <input type="text" name="flat" required maxlength="50" value="<?php echo $flat ?>" placeholder="Nhập tên đường, tòa nhà, số nhà" class="input">
                        </div>
                    </div>
                </div>
                <?php if ($name == ''){ ?>
                    <button type="submit" name="them" class="btn">Thêm</button>
                <?php } else { ?>
                    <button type="submit" name="capnhat" class="btn">Cập nhật</button>
                <?php } ?>
            </form>    
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function validateForm() {
                const tinh = document.getElementById('tinh').value;
                const huyen = document.getElementById('huyen').value;
                const xa = document.getElementById('xa').value;
                

                if (!tinh || tinh === "") {
                    alert("Vui lòng chọn Tỉnh/Thành phố.");
                    return false; // Ngăn không cho gửi biểu mẫu
                }
                if (!huyen || huyen === "") {
                    alert("Vui lòng chọn Quận/Huyện.");
                    return false; // Ngăn không cho gửi biểu mẫu
                }
                if (!xa || xa === "") {
                    alert("Vui lòng chọn Xã/Phường/Thị trấn.");
                    return false; // Ngăn không cho gửi biểu mẫu
                }

                return true; // Cho phép gửi biểu mẫu
            }

            
        // Lắng nghe sự kiện thay đổi trên select tỉnh
        document.getElementById('tinh').addEventListener('change', function() {
            const tinhId = this.value;
            console.log('Fetching districts for province ID:', tinhId); // In ra ID tỉnh

            if (!tinhId) {
                huyenSelect.innerHTML = '<option disabled selected>Chọn Quận/Huyện</option>'; // Không có value
                huyenSelect.disabled = true; // Vô hiệu hóa huyện
                xaSelect.innerHTML = '<option value="" disabled selected>Chọn Xã/Phường/Thị trấn</option>';
                xaSelect.disabled = true; // Vô hiệu hóa xã
                return;
            }

            fetch('get_huyen.php?tinh_id=' + tinhId)
                .then(response => response.json())
                .then(data => {
                    console.log('Districts received:', data); // In ra dữ liệu huyện
                    const huyenSelect = document.getElementById('huyen');
                    huyenSelect.innerHTML = ''; // Reset các option huyện
                    huyenSelect.disabled = false; 
                    // Kiểm tra nếu có dữ liệu huyện
                    if (data.length > 0) {
                        data.forEach(huyen => {
                            // Tạo các option cho dropdown huyện
                            const option = document.createElement('option');
                            option.value = huyen.id; // ID huyện
                            option.textContent = huyen.name; // Tên huyện
                            huyenSelect.appendChild(option); // Thêm option vào dropdown
                        });
                    } else {
                        // Nếu không có huyện, thêm một option mặc định
                        huyenSelect.innerHTML += '<option value="">Không có huyện nào</option>';
                        huyenSelect.disabled = true;
                    }
                    
                    // Reset danh sách xã mỗi khi tỉnh thay đổi
                    document.getElementById('xa').innerHTML = '<option value="">Chọn Xã/Phường/Thị trấn</option>'; // Reset danh sách xã
                })
                .catch(error => console.error('Error fetching districts:', error));
        });

        // Lắng nghe sự kiện thay đổi trên select huyện
        document.getElementById('huyen').addEventListener('change', function() {
            const huyenId = this.value; // Lấy giá trị ID huyện được chọn

            // Reset danh sách xã
            const xaSelect = document.getElementById('xa');
            xaSelect.innerHTML = ''; // Thiết lập lại danh sách xã

            if (huyenId) {
                fetch('get_xa.php?huyen_id=' + huyenId)
                    .then(response => response.json()) // Chuyển đổi phản hồi sang JSON
                    .then(data => {
                        console.log('Fetched wards:', data); // In ra dữ liệu xã đã lấy
                        xaSelect.disabled = false;
                        // Thêm các option vào select xã
                        data.forEach(xa => {
                            const option = document.createElement('option');
                            option.value = xa.id; // Thay đổi 'id' nếu tên cột khác
                            option.textContent = xa.name; // Thay đổi 'name' nếu tên cột khác
                            xaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching wards:', error));
            }
        });

    </script>
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