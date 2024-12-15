<header class="header"> 
   <section class="flex">
        <a href="home.php" class="logo">
            <img src="images/logo.png" width="90px">
        </a>
        <nav class="navbar">
            <ul id="main-menu">
                <li><a href="" id="drop-menu">Danh mục</a>
                    <ul class="sub-menu">
                        <?php
                            $select_categories = $conn->prepare("SELECT * FROM `danhmuc` WHERE trangthai = 'Hiện' ORDER BY CASE WHEN name = 'phụ kiện khác' THEN 1 ELSE 0 END, name ASC");
                            $select_categories->execute();

                            // Lấy kết quả và hiển thị
                            while ($category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <form action="" method="post">
                            <li onclick="window.location.href='cate_product.php?category_id=<?= $category['danhmuc_id']; ?>'"><?= $category['name']; ?></li>
                        </form>
                        <?php
                            }
                        ?>
                    </ul>
                </li>
                
                <li><a href="menu.php">Tất cả sản phẩm</a></li>
                <li><a href="" id="drop-menu">Bộ sưu tập</a>
                    <ul class="sub-menu">
                        <?php
                            $select_anhbst = $conn->prepare("SELECT * FROM `anhbst`");
                            $select_anhbst->execute();

                            while ($fetch_anhbst = $select_anhbst->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <form action="" method="get">
                            <li onclick="window.location.href='collection.php?id=<?= $fetch_anhbst['id']; ?>'"><?= $fetch_anhbst['name']; ?></li>
                        </form>
                        <?php
                            }
                        ?>
                    </ul>
                </li> 
                <li class="personal-choice">
                    <a href="search_preferences.php">Gợi ý theo sở thích cá nhân</a>
                    <span class="new-label">NEW</span>
                </li>
                <!-- <li><a href="home.php#resume" class="about-us-btn" id="drop-menu">Về chúng tôi</a></li> -->
            </ul>
        </nav>
        
        
        <form action="search_product.php" method="post" class="search-form"> 
            <input type="text" name="search_product" placeholder="Tìm kiếm..." required maxlength="100">
            <button type="submit" class="bi bi-search" id="search_product_btn"></button>
        </form>
        <div class="icons">
            <div class="bi bi-list" id="menu-btn"></div>
            <div class="bi bi-search" id="search-btn"></div>

            <?php
                $count_wish_item = $conn->prepare("SELECT * FROM `love` WHERE user_id=?");
                $count_wish_item->execute([$user_id]);
                $total_wish_items = $count_wish_item->rowCount();
            ?>
            <a href="wishlist.php"><i class="bi bi-heart-fill"></i><sup><?= $total_wish_items;?></sup></a>
            
            <?php
                $count_cart_item = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
                $count_cart_item->execute([$user_id]); 
                $total_cart_items = $count_cart_item->rowCount();
            ?>
            <a href="cart.php"><i class="bi bi-bag-fill"></i><sup><?= $total_cart_items;?></sup></a>
            <!-- <a href="profile.php"><i class="fa-solid fa-user"></i></a> -->
            <a href="message.php"><i class="bi bi-chat-dots-fill"></i></a>
            <div class="bi bi-person-fill" id="user-btn"></div>
        </div>
        
        <div class="profile-detail">
            <?php 
                $select_profile = $conn->prepare("SELECT * FROM `user` WHERE user_id = ? AND vaitro = 'khach'");
                $select_profile->execute([$user_id]);

                if ($select_profile->rowCount() > 0) {
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="profile-points">
                <h3 style="text-transform: uppercase;"><?= $fetch_profile['name']; ?></h3>
                <p><strong>Tổng chi tiêu: </strong><?=number_format($fetch_profile['tiendamua'], 0, ',', '.'); ?> VNĐ</p>
                <p><strong>Điểm thưởng hiện có: </strong><?= $fetch_profile['diem']; ?></p>
                <p>(khi tích đủ 70 điểm sẽ được giảm 10% trên tổng giá trị đơn hàng)</p>
            </div>
            <div class="flex-btn">
                <a href="user_update.php" class="btn">Sửa hồ sơ</a>
            </div>
            <div class="flex-btn">
                <!-- <a href="profile.php" class="btn">Tài khoản của tôi</a> -->
                <a href="component/user_logout.php" onclick="return confirm('logout from this website');" class="btn">
                    Đăng xuất
                </a>
            </div>
            <div class="flex-btn">
                <a href="order.php" class="btn">Đơn hàng</a>
            </div>
            <?php }else{ ?>
                <h3 style="margin-bottom: 1rem;">Vui lòng đăng nhập hoặc đăng ký</h3>
                <div class="flex-btn">
                    <a href="login.php" class="btn">Đăng nhập</a>
                </div>
                <div class="flex-btn">
                    <!-- <a href="user_login.php" class="btn">Đăng nhập</a> -->
                    <a href="user_register.php" class="btn">Đăng ký</a>
                </div>
            <?php } ?>
        </div>
    </section>
    
</header>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.querySelector('.header');

        // Kiểm tra xem phần tử header có tồn tại không
        if (header) {
            console.log('Header found!');

            // Lắng nghe sự kiện khi chuột vào header
            header.addEventListener('mouseenter', () => {
                console.log('Mouse entered header!');
                header.classList.add('visible');  // Thêm class 'visible' khi di chuột vào
            });

            // Lắng nghe sự kiện khi chuột ra khỏi header
            header.addEventListener('mouseleave', () => {
                console.log('Mouse left header!');
                header.classList.remove('visible');  // Loại bỏ class 'visible' khi chuột ra khỏi header
            });
        } else {
            console.log('Header not found!');
        }
    });
    // Kiểm tra nếu URL có chứa #resume
if (window.location.hash === '#resume') {
    window.addEventListener('load', function() {
        // Cuộn mượt mà đến phần có class 'resume'
        document.querySelector('.resume').scrollIntoView({
            behavior: 'smooth'
        });
    });
}


</script>