
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
                $fetch_profile = [];
                if ($select_profile->rowCount() > 0) {
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                }
            ?>
            <div class="profile">
                <p><?= $fetch_profile['name']; ?></p>
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
            <p>(Nhân viên)</p>
        </div>
        <?php ?>
        <div class="navbar">
            <ul>
                <!-- <li onclick="window.location.href='../admin_panel/dashboard.php'"><i class="bi bi-house-door-fill" style="margin-right: .7rem;"></i>Trang chủ</li> -->
                <li onclick="window.location.href='employee_order.php'"><i class="bi bi-receipt" style="margin-right: .7rem;"></i>Đơn hàng</li>     
                <li onclick="window.location.href='all_messages.php'"><i class="bi bi-chat-dots-fill" style="margin-right: .7rem;"></i>Tin nhắn</li> 
                <li onclick="window.location.href='all_reviews.php'"><i class="bi bi-pen-fill" style="margin-right: .7rem;"></i>Đánh giá</li>
            </ul> 
        </div>
    </div>
</div>