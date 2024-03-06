
<header> 
    <div class="logo">
        <img src="../images/slogan_for_admin.png" width="140">
    </div>
    <div class="right">
        <div class="fas fa-user" id="user-btn"></div>
        <div class="toggle-btn">
            <i class="fa-solid fa-bars"></i>
        </div>
        <div class="profile-detail">
            <?php 
                $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE admin_id = ?");
                $select_profile->execute([$admin_id]);
                $fetch_profile = []; // Khởi tạo một mảng trống để tránh lỗi Undefined Variable
                if ($select_profile->rowCount() > 0) {
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                }
            ?>
            <div class="profile">
                <p><?= $fetch_profile['name']; ?></p> 
                <div class="flex-btn">
                    <a href="profile.php" class="btn">Tài khoản của tôi</a>
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
            $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE admin_id = ?");
            $select_profile->execute([$admin_id]);

            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            }
        ?>
        <div class="profile">
            <p><?= $fetch_profile['name']; ?></p>
        </div>
        <?php ?>
        <h5>Menu</h5>
        <div class="navbar">
            <ul>
                <li onclick="window.location.href='dashboard.php'"><i class="fa fa-home" style="margin-right: .7rem;"></i>dashboard</li>
                <li onclick="window.location.href='type.php'"><i class="fa fa-list-alt" style="margin-right: .7rem;"></i>Danh mục-Thương hiệu</li>
                <li onclick="window.location.href='add_product.php'"><i class="fas fa-cart-plus" style="margin-right: .7rem;"></i>Thêm sản phẩm</li>
                <li onclick="window.location.href='view_product.php'"><i class="fa-solid fa-square-check" style="margin-right: .7rem;"></i>Sản phẩm đã thêm</li>
                <li onclick="window.location.href='all_acc.php'"><i class="fa fa-address-book" style="margin-right: .7rem;"></i>Tài khoản người dùng</li>
            </ul>
        </div>
        <h5>theo dõi chúng tôi trên</h5>
        <div class="social-links">
            <i class="fa-brands fa-facebook"></i>
            <i class="fab fa-instagram"></i>
            <i class="fa-brands fa-tiktok"></i>
        </div>
    </div>
</div>