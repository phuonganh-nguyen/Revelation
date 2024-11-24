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
    <title>révélation - Delivery - policy page</title>
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
                <h1>Chính sách Giao - Nhận - Vận chuyển</h1>
            </div> 
            <div class="">
                <h3>1. Cách thức thanh toán chi phí vận chuyển</h3>
                <div class="step">
                    <p>Thanh toán trực tiếp khi nhận hàng: Trả tiền hàng và phí vận chuyển cho nhân viên giao hàng.</p>
                    <p>Thanh toán trực tuyến: Thanh toán toàn bộ chi phí (tiền hàng + phí vận chuyển) ngay trên website khi đặt hàng. Phí vận chuyển được áp dụng như sau:</p>
                    <ul>
                        <li>Giao Hàng Tiết Kiệm (GHTK): 25.000 VNĐ.</li>
                        <li>Giao Hàng Nhanh (GHN): 30.000 VNĐ.</li>
                    </ul>
                </div>
            </div>
            <div>
                <h3>2. Chính sách nhận hàng</h3>
                <div>
                    <p>Kiểm tra niêm phong: Quý khách vui lòng chỉ nhận hàng khi đơn hàng còn nguyên vẹn, đầy đủ niêm phong.</p>
                    <p>Không hỗ trợ đồng kiểm: RÉVÉLATION không hỗ trợ kiểm tra hàng trước khi nhận. Nếu phát hiện hàng lỗi sản xuất, thiếu hàng, hoặc sai sản phẩm, Quý khách vui lòng quay video quá trình mở gói hàng và gửi về email <span>loverevelationshop@gmail.com</span> để được hỗ trợ đổi/trả.</p>
                </div>
            </div>
            <div class="">
                <h3>3. Chính sách giao hàng</h3>
                <div class="step">
                    <p>RÉVÉLATION hiện liên kết với các đơn vị vận chuyển:</p>
                    <ul>
                        <li>Giao Hàng Tiết Kiệm (GHTK)</li>
                        <li>Giao Hàng Nhanh (GHN)</li>
                    </ul>
                    <p>Thời gian giao hàng dự kiến</p>
                    <ul>
                        <li>Thời gian giao hàng thường từ 3 - 7 ngày làm việc (không tính ngày lễ, Tết) kể từ khi RÉVÉLATION bàn giao đơn hàng cho đơn vị vận chuyển.</li>
                        <li>Lưu ý: Với các khu vực đặc biệt như vùng sâu, vùng xa, vùng đảo hoặc trong trường hợp bất khả kháng (thời tiết, dịch bệnh), thời gian giao hàng có thể kéo dài. RÉVÉLATION sẽ thông báo chi tiết qua email hoặc điện thoại nếu có thay đổi về thời gian giao.</li>
                    </ul>
                    <p>Quý khách có thể theo dõi trạng thái đơn hàng thông qua:</p>
                    <ul>
                        <li>Trang quản lý đơn hàng trên website.</li>
                        <li>Email thông báo tự động (nếu Quý khách chọn nhận thông báo qua email).</li>
                    </ul>
                </div>
            </div>
            <div class="">
                <h3>4. Chính sách hỗ trợ khi giao hàng gặp sự cố</h3>
                <div class="step">
                    <p>Nếu đơn hàng bị hủy do không thể giao vận vì yếu tố khách quan:</p>
                    <ul>
                        <li>Thông báo qua email hoặc điện thoại.</li>
                        <li>Hoàn tiền đối với các đơn hàng đã thanh toán trước.</li>
                    </ul>
                </div>
                <h3 style="margin-top: 2rem;">Nếu Quý khách cần thêm hỗ trợ, vui lòng liên hệ qua email <span>loverevelationshop@gmail.com</span> hoặc gọi hotline <span>1800 3015</span>.</h3>
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