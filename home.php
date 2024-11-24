<?php 
    include 'component/connect.php';
    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
    }

    include 'component/add_wishlist.php';
    include 'component/add_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Home page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
    <?php include 'component/user_header.php'; ?>
    <!-- slider section star -->
    <!-- <div class="slider-container"> -->
    <div class="hero">
        <!-- <div class="textBox">
            <h1>Bộ sưu tập Cruise 2024</h1>
            <a href="https://eu.louisvuitton.com/eng-e1/stories/cruise-2024-collection?utm_source=youtube&utm_medium=social&utm_campaign=PUBL_CRUISE2024DROP2_YT_WW_ENG20240109_WOM_MULTI" class="btn">Khám phá ngay</a>
        </div> -->
        <video autoplay loop muted plays-inline class="back-video">
            <source src="images/Banner/final1.mp4" type="video/mp4">          
        </video>
        <!-- <img src="images/cruise2.jpg" class="back-video"> -->
    </div> 

    
    <div class="products-new" style="margin-top: 1rem;">
        <div class="heading">
            <h1>Sản phẩm mới về</h1>
        </div>
        <?php include 'new_product.php'; ?>
    </div>
    <div class="yoshi" style="margin-top: -4rem;">
        <img src="images/Banner/mai2.jpg" class="back-video" >
        <!-- <a href="http://localhost/web/collection.php?id=12"><img src="images/Banner/mai2.jpg" class="back-video" ></a> -->
    </div>
    <div class="products-new" >
        <div class="heading" style="margin-top: -2.5rem; margin-bottom: 1rem;">
            <h1>Sản phẩm bán chạy</h1>
        </div>
        <?php include 'best_seller.php'; ?>
    </div>
    <div class="yoshi" style="margin-top: -4rem;">
        <img src="images/Banner/tieuvy1.jpg" class="back-video" >
        <a href="http://localhost/web/collection.php?id=13" class="btn">Khám phá ngay</a>
    </div>
    <?php include 'resume.php' ?> 
    <section class="why-choose-us" style="margin-bottom: -3rem;">
        <div class="overlay"></div>
        <h2 class="title">LÝ DO BẠN NÊN CHỌN RÉVÉLATION</h2>
        <div class="boxes">
            <div class="box">
                <h2>Chất lượng vượt trội và bền bỉ</h2>
                <p>Sản phẩm của Ré luôn được lựa chọn kỹ lưỡng từ nguyên liệu cao cấp và quy trình sản xuất tỉ mỉ, giúp đảm bảo độ bền lâu dài. Ré cam kết mang đến cho nàng những sản phẩm đáng tin cậy, để nàng luôn cảm thấy yên tâm sử dụng lâu dài mà không phải lo lắng về chất lượng.</p>
            </div>
            <div class="box">
                <h2>Thiết kế tinh tế, thời thượng</h2>
                <p>Mỗi sản phẩm của Ré đều được thiết kế với tâm huyết, mang đến vẻ đẹp hiện đại và thanh lịch. Với từng chi tiết được chăm chút cẩn thận, các sản phẩm không chỉ dễ dàng kết hợp với nhiều trang phục mà còn giúp nàng tự tin thể hiện phong cách cá nhân, từ những buổi dạo phố đến những dịp quan trọng.</p>
            </div>
            <div class="box">
                <h2>Trải nghiệm mua sắm tuyệt vời</h2>
                <p>Khi nàng chọn Ré, nàng không chỉ mua được sản phẩm chất lượng mà còn trải nghiệm cảm giác mua sắm thoải mái và hài lòng. Mỗi sản phẩm là một lời hứa, một sự chăm sóc dành riêng cho nàng, để nàng cảm nhận được sự khác biệt ngay từ lần đầu tiên.</p>
            </div>
        </div>
    </section>
    <script src="js/sweetalert.js"></script>
    <script src="js/user_script.js" defer></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    
    <?php include 'component/alert.php'; ?>
    <?php include './component/footer.php'?>
</body>
</html>