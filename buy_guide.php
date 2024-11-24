<?php 
    include 'component/connect.php';
    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Buy - guide page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
    <?php include 'component/user_header.php'; ?>
    <div class="guide-page">
        <div class="banner-doc">
            <img src="images/Banner/banner_doc.png" alt="">
        </div>
        <div class="guide">
            <div class="heading">
                <h1>Hướng dẫn mua hàng</h1>
            </div> 
            <div class="">
                <h3>Mua sắm tại RÉVÉLATION thật dễ dàng. Quý khách chỉ cần làm theo các bước sau:</h3>
                <div class="step">
                    <p>Bước 1: Đăng ký tài khoản (nếu chưa có):</p>
                    <ul>
                        <li>Chọn nút Đăng ký và điền thông tin cần thiết (họ tên, email, mật khẩu).</li>
                        <li>Sau khi đăng ký thành công, bạn có thể đăng nhập bằng tài khoản vừa tạo.</li>
                    </ul>
                    <p>Bước 2: Đăng nhập tài khoản:</p>
                    <ul>
                        <li>Nếu đã có tài khoản, chọn nút Đăng nhập và nhập email/mật khẩu để truy cập vào tài khoản cá nhân.</li>
                    </ul>
                    <p>Bước 3: Duyệt qua các danh mục hoặc sử dụng thanh tìm kiếm để tìm sản phẩm mà bạn quan tâm.</p>
                    <p>Bước 4: Xem chi tiết sản phẩm:</p>
                    <ul>
                        <li>Nhấn vào sản phẩm để xem các thông tin chi tiết như chất liệu, màu sắc, phong cách, dịp lý tưởng.</li>
                        <li>Rê chuột vào ảnh để xem kỹ các họa tiết và đường may của sản phẩm.</li>
                    </ul>
                    <p>Bước 5: Thêm sản phẩm vào giỏ hàng:</p>
                    <ul>
                        <li>Chọn size và số lượng mong muốn.</li>
                        <li>Nhấn nút Thêm vào giỏ hàng.</li>
                        <li>Bạn có thể tiếp tục mua sắm hoặc tiến hành xử lý đơn hàng.</li>
                    </ul>
                    <p>Bước 6: Xử lý đơn hàng:</p>
                    <ul>
                        <li>Khi đã hoàn tất chọn sản phẩm, vào giỏ hàng và nhấn nút Tiến hành thanh toán.</li>
                        <li>Nhập địa chỉ nhận hàng.</li>
                        <li>Chọn đơn vị vận chuyển phù hợp.</li>
                        <li>Nếu muốn nhận thông báo qua email, chọn Nhận thông báo qua email (tùy chọn).</li>
                        <li>Chọn phương thức thanh toán (Thanh toán bằng tiền mặt hoặc qua Ví điện tử Momo) sau đó là hoàn thành.</li>
                    </ul>
                </div>
            </div>
            <div>
                <h3>Nếu Quý khách muốn thay đổi đơn hàng (thêm mặt hàng, thay đổi địa chỉ):</h3>
                <div>
                    <p>Trường hợp 1: Đơn hàng chưa được xác nhận</p>
                    <ul>
                        <li>Vào mục Đơn hàng trên tài khoản cá nhân.</li>
                        <li>Nhấn vào đơn hàng cần chỉnh sửa, sau đó chọn Hủy đơn hàng.</li>
                        <li>Sau khi hủy, bạn có thể đặt lại đơn hàng mới với các thay đổi mong muốn.</li>
                    </ul>
                    <p>Trường hợp 2: Đơn hàng đã được xác nhận</p>
                    <ul>
                        <li>Gọi ngay đến số 1800 3015 để được hỗ trợ.</li>
                    </ul>
                </div>
                <h3 style="margin-top: 2rem;">RÉVÉLATION rất vui vì được Quý khách tin yêu!</h3>
            </div>
        </div>    
    </div>

    <script src="js/sweetalert.js"></script>
    <script src="js/user_script.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    
    <?php include 'component/alert.php'; ?>
    <?php include './component/footer.php'?>
</body>
</html>