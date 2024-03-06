<header class="header"> 
    <!-- <div href="home.php" class="logo">
        <img src="images/slogan_for_user.png" width="350">
    </div> -->
    <section class="flex">
        <a href="home.php" class="logo">
            <img src="images/slogan_for_user.png" width="350px">
        </a>
        <nav class="navbar">
            <!-- <a href="home.php">Home</a> -->
            <a href="menu.php">Sản phẩm</a> 
            <!-- <div class="type">
                <a href="" data-type="face">Trang điểm mặt</a>
                <a href="">Trang điểm môi</a>
                <a href="">Mặt nạ</a>
                <a href="">Làm sạch da</a>
                <a href="">Dưỡng da</a>
                <a href="">Khác</a>
            </div> -->
            <!-- <a href="menu.php" id="type">Thương hiệu</a> -->
            <a href="order.php">Đơn hàng</a>
            <a href="about-us.php">Về chúng tôi</a>
            <a href="contact.php">Liên hệ</a>
        </nav>
        <form action="search_product.php" method="post" class="search-form"> 
            <input type="text" name="search_product" placeholder="Tìm kiếm..." required maxlength="100">
            <button type="submit" class="fas fa-search" id="search_product_btn"></button>
        </form>
        <div class="icons">
            <div class="fa-solid fa-list" id="menu-btn"></div>
            <div class="fas fa-search" id="search-btn"></div>

            <?php
                $count_wish_item = $conn->prepare("SELECT * FROM `yeuthich` WHERE user_id=?");
                $count_wish_item->execute([$user_id]);
                $total_wish_items = $count_wish_item->rowCount();
            ?>
            <a href="wishlist.php"><i class="fa-solid fa-heart"></i><sup><?= $total_wish_items;?></sup></a>
            
            <?php
                $count_cart_item = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
                $count_cart_item->execute([$user_id]);
                $total_cart_items = $count_cart_item->rowCount();
            ?>
            <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i><sup><?= $total_cart_items;?></sup></a>
            <!-- <a href="profile.php"><i class="fa-solid fa-user"></i></a> -->
            
            <div class="fa-solid fa-user" id="user-btn"></div>
        </div>
        <div class="profile-detail">
            <?php 
                $select_profile = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
                $select_profile->execute([$user_id]);

                if ($select_profile->rowCount() > 0) {
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            
            <h3 style="margin-bottom: 1rem; text-transform: capitalize;"><?= $fetch_profile['name']; ?></h3>
            <div class="flex-btn">
                <a href="profile.php" class="btn">Tài khoản của tôi</a>
                <!-- <a href="component.user_logout.php" onclick="return confirm('logout from this website');" class="btn">
                    Đăng xuất
                </a> -->
            </div>
            <div class="flex-btn">
                <!-- <a href="profile.php" class="btn">Tài khoản của tôi</a> -->
                <a href="component/user_logout.php" onclick="return confirm('logout from this website');" class="btn">
                    Đăng xuất
                </a>
            </div>
            <?php }else{ ?>
                <h3 style="margin-bottom: 1rem;">Vui lòng đăng nhập hoặc đăng ký</h3>
                <div class="flex-btn">
                    <a href="user_login.php" class="btn">Đăng nhập</a>
                    <!-- <a href="user_register.php" class="btn">Đăng ký</a> -->
                </div>
                <div class="flex-btn">
                    <!-- <a href="user_login.php" class="btn">Đăng nhập</a> -->
                    <a href="user_register.php" class="btn">Đăng ký</a>
                </div>
            <?php } ?>
        </div>
    </section>
</header>