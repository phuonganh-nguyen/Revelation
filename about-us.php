<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else{
        $user_id = '';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrect Beauty - Home page</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
</head>
<body>
    <?php include 'component/user_header.php' ?>
     
    <div class="heading" style="margin-top: 10rem;">
        <h1>Giới thiệu về Secret Beauty</h1>
        <p> Secret Beauty là nơi bạn có thể khám phá và mua sắm các sản phẩm mỹ phẩm chất lượng và chuyên nghiệp từ những thương hiệu hàng đầu trên thị trường. Chúng tôi tự hào mang đến cho khách hàng những trải nghiệm mua sắm trực tuyến tuyệt vời nhất với đội ngũ chăm sóc khách hàng tận tâm và đảm bảo về chất lượng sản phẩm.</p>
    </div>

    <div class="left" style="margin-top: 4rem;">
        <div class="box">
            <div class=""> 
                <img src="images/quality.png">
            </div>
            <div class="abc">
                <h2>Sự đa dạng và chất lượng cao</h2>
                <p>Secret Beauty cung cấp một bộ sưu tập đa dạng các sản phẩm mỹ phẩm từ các thương hiệu nổi tiếng trên thế giới. Tất cả sản phẩm đều được chọn lựa kỹ lưỡng để đảm bảo chất lượng và hiệu quả.</p>
            </div>
        </div>
    </div>
    <div class="right">
        <div class="box" style="margin-right: 10px;">
            <div class="abc">
                <h2>Tư vấn chăm sóc da chuyên nghiệp</h2>
                <p>Chúng tôi hiểu rằng mỗi người có làn da khác nhau. Vì vậy, Secret Beauty không chỉ là nơi để mua sắm, mà còn là nguồn thông tin và tư vấn chăm sóc da chuyên nghiệp. Chúng tôi cung cấp thông tin chi tiết về sản phẩm và bài viết hữu ích để giúp bạn chọn lựa phù hợp nhất.</p>
            </div>
            <div class=""> 
                <img src="images/skin.png">
            </div>
        </div>
    </div>
    <div class="left">
        <div class="box">
            <div class=""> 
                <img src="images/discount.png" style="max-width: 60%;">
            </div>
            <div class="abc">
                <h2>Ưu đãi hấp dẫn</h2>
                <p>Secret Beauty thường xuyên có các chương trình khuyến mãi, giảm giá và quà tặng để tri ân khách hàng. Điều này giúp bạn tiết kiệm chi phí và trải nghiệm mua sắm thú vị hơn.</p>
            </div>
        </div>
    </div>
    <div class="right">
        <div class="box" style="margin-right: 10px;">
            <div class="abc">
                <h2>Giao hàng nhanh chóng và an toàn</h2>
                <p>Chúng tôi cam kết đưa sản phẩm đến tay bạn nhanh chóng và an toàn. Hệ thống giao hàng của chúng tôi được thiết lập để đảm bảo sản phẩm luôn đến trong tình trạng hoàn hảo.</p>
            </div>
            <div class=""> 
                <img src="images/fastdelivery.png">
            </div>
        </div>
    </div>
    
    <div class="left">
        <div class="box"  style="margin-bottom: 5rem;">
            <div class=""> 
                <img src="images/security.png" style="max-width: 40%;">
            </div>
            <div class="abc">
                <h2>Bảo mật thanh toán</h2>
                <p>Đảm bảo an toàn và bảo mật trong quá trình thanh toán trực tuyến để tạo niềm tin cho người dùng.</p>
            </div>
        </div>
    </div>

    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>