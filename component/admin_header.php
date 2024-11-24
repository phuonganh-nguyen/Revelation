
<header> 
    <div class="logo">
        <img src="../images/logo.png" width="90px">
    </div>
    <div class="right">
        <div class="bi bi-person-fill" id="user-btn"></div>
        <div class="toggle-btn">
            <i class="bi bi-list"></i>
        </div>
        <div class="profile-detail">
            <?php 
                $select_profile = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
                $select_profile->execute([$user_id]);
                $fetch_profile = []; // Khởi tạo một mảng trống để tránh lỗi Undefined Variable
                if ($select_profile->rowCount() > 0) {
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                }
            ?>
            <div class="profile">
                <p><?= $fetch_profile['name']; ?></p> 
                <div class="flex-btn">
                    <a href="update.php" class="btn">Sửa hồ sơ</a>
                </div>
                <div class="flex-btn">
                    <a href="../component/admin_logout.php" onclick="return confirm('logout from this website?');" class="btn">Đăng xuất</a>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="sidebar-container">
    <div class="sidebar">
        <?php 
            $select_profile = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
            $select_profile->execute([$user_id]);

            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            }
        ?>
        <div class="profile">
            <p><?= $fetch_profile['name']; ?></p>
        </div>
        <?php ?>
        <!-- <h5>Menu</h5> -->
        <div class="navbar">
            <ul>
                <li onclick="window.location.href='dashboard.php'"><i class="bi bi-house-door-fill" style="margin-right: .7rem;"></i>Trang chủ</li>
                <li onclick="window.location.href='add_category.php'"><i class="bi bi-card-list" style="margin-right: .7rem;"></i>Danh mục</li>
                <li onclick="window.location.href='add_product.php'"><i class="bi bi-bag-plus-fill" style="margin-right: .7rem;"></i>Thêm sản phẩm</li>
                <li onclick="window.location.href='view_product.php'"><i class="bi bi-bag-check-fill" style="margin-right: .7rem;"></i>Sản phẩm đã thêm</li>
                <li onclick="window.location.href='admin_order.php'"><i class="bi bi-receipt" style="margin-right: .7rem;"></i>Đơn hàng</li>
                
                <li onclick="window.location.href='all_reviews_admin.php'"><i class="bi bi-pen-fill" style="margin-right: .7rem;"></i>Đánh giá</li>
                <li onclick="window.location.href='revenue_report.php'"><i class="bi bi-graph-up-arrow" style="margin-right: .7rem;"></i>Báo cáo</li>
            </ul> 
        </div>
        <!-- <h5>theo dõi chúng tôi trên</h5>
        <div class="social-links">
            <i class="fa-brands fa-facebook"></i>
            <i class="fab fa-instagram"></i>
            <i class="fa-brands fa-tiktok"></i>
        </div> -->
    </div>
</div>