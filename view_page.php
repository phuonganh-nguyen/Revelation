<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else{
        $user_id = '';
    }
    if (isset($_GET['pid']) || isset($_GET['get_id'])) {
        // Khai báo biến pid và get_id
        $pid = isset($_GET['pid']) ? $_GET['pid'] : '';
        $get_id = isset($_GET['get_id']) ? $_GET['get_id'] : '';

        // Khởi tạo biến tham số cho execute
        $parameter = !empty($pid) ? $pid : $get_id;
    }

    include 'component/add_wishlist.php';
    include 'component/add_cart.php';

    // Lấy tên danh mục của sản phẩm đang xem
    $select_category = $conn->prepare("SELECT loaisp FROM `sanpham` WHERE sanpham_id=?");
    $select_category->execute([$pid]);
    $category = $select_category->fetchColumn();

    // Lấy tên danh mục của sản phẩm đang xem
    $brand = $conn->prepare("SELECT thuonghieu FROM `sanpham` WHERE sanpham_id=?");
    $brand->execute([$pid]);
    $thuonghieu = $brand->fetchColumn();

    // Lấy sản phẩm đang xem
    $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
    $select_products->execute([$pid]);
    
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    if (isset($_POST['send_mess'])) {
        if (!empty($user_id)) {
            $message = $_POST['product_id'];

            // Kiểm tra nội dung tin nhắn
            if (!empty($message)) {
                // Lấy thời gian hiện tại theo múi giờ Việt Nam
                $current_time = date('Y-m-d H:i:s');

                $insert_message = $conn->prepare("INSERT INTO `send_message` (user_id, sanpham_id, ngaygui) VALUES (?, ?, ?)");
                $insert_message->execute([$user_id, $message, $current_time]);
                header('location:message.php');
            } else {
                $warning_msg[] = 'Vui lòng nhập nội dung tin nhắn!';
            }
        } else {
            $warning_msg[] = 'Vui lòng đăng nhập!';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Product detail page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'component/user_header.php'; ?>
    <section class="view_page">
        <?php
        if (isset($_GET['pid']) || isset($_GET['get_id'])) {
            $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
            $select_products->execute([$parameter]);

            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                    $select_detail = $conn->prepare("SELECT * FROM `motasanpham` WHERE sanpham_id=?");
                    $select_detail->execute([$parameter]);
                    $fetch_detail = $select_detail->fetch(PDO::FETCH_ASSOC);
    
        ?> 
           
            <form action="" method="post" class="box">
                <div class="img-box">
                <figure>
                    <img src="uploaded_files/<?= $fetch_products['image']; ?>" id="mainImage" alt="Product Image">
                </figure>

                    <div class="thumb-list">
                        <ul>
                            <li>
                                <img src="uploaded_files/<?= $fetch_products['image']; ?>" id="thumb">
                                <?php
                                    // Lấy tất cả các hình ảnh có khóa ngoại là sanpham_id của sản phẩm hiện tại
                                    $select_images = $conn->prepare("SELECT * FROM `anhsp` WHERE sanpham_id=?");
                                    $select_images->execute([$fetch_products['sanpham_id']]);
                                    while ($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<img src="uploaded_files/' . $fetch_images['img_path'] . '" id="thumb">';
                                    }
                                ?>
                                <img src="images/size.jpg" alt="" id="thumb">
                            </li>
                        </ul>
                    </div>
                    <div class="icon">
                        <p>
                        <button type="submit" name="add_to_wishlist" style="font-size: 1rem; background-color: transparent;"><i class="bi bi-heart-fill"></i></button>    
                            Đã bán: <?= $fetch_products['luotmua']; ?>
                        </p>
                        <p>
                        <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                        <button type="submit" name="send_mess" style="font-size: 1rem; background-color: transparent; cursor: pointer;"><i class="bi bi-chat-dots-fill"></i> Tư vấn</button>    
                            
                        </p>
                        <!-- <p>Chia sẻ: 
                            <i class="bi bi-facebook"></i> 
                            <i class="bi bi-messenger"></i> 
                            <i class="bi bi-instagram"></i> 
                        </p> -->
                    </div>
                    
                     
                </div>
                <div class="detail">
                    <?php
                        // Lấy số lượng từ các cỡ sản phẩm và cộng lại với nhau
                        $total_quantity = $fetch_products['sizeS'] + $fetch_products['sizeM'] + $fetch_products['sizeL'] + $fetch_products['sizeXL'] + $fetch_products['freesize'];

                        // Kiểm tra trạng thái dựa trên tổng số lượng
                        if ($total_quantity > 9) {
                            echo '<span class="soluong" style="color: green;">Có sẵn</span>';
                        } elseif ($total_quantity == 0) {
                            echo '<span class="soluong" style="color: red;">Hết hàng</span>';
                        } else {
                            echo '<span class="soluong" style="color: red;">Chỉ còn ' . $total_quantity . ' sản phẩm</span>';
                        }
                        ?>

                    
                    <div class="name" style="text-transform: uppercase;"><?= $fetch_products['name']; ?></div>
                    <p class="product-detail">Giá: <?= number_format($fetch_products['price'], 0, ',', '.') ?> VNĐ</p>
                    <p class="product-detail">Xuất xứ: <?= $fetch_products['xuatxu']; ?></p>
                    <p class="product-detail">Màu sắc: 
                        <?php 
                            // Hiển thị color_1
                            echo trim($fetch_detail['color_1']);
                            
                            // Nếu color_2 khác 0, thêm dấu phẩy và hiển thị color_2
                            if ($fetch_detail['color_2'] != '0') {
                                echo ', ' . trim($fetch_detail['color_2']);
                            }
                        ?>
                    </p>
                    <p class="product-detail">Họa tiết: <?= $fetch_detail['hoatiet']; ?></p>
                    <p class="product-detail">Chất liệu: 
                        <?php 
                            echo trim($fetch_detail['chatlieu_1']);
                            if ($fetch_detail['chatlieu_2'] != '0') {
                                echo ', ' . trim($fetch_detail['chatlieu_2']);
                            }
                        ?>
                    </p>

                    <p class="product-detail">Phong cách: 
                    
                        <?php 
                            echo trim($fetch_detail['style_1']);
                            // Kiểm tra và hiển thị style_2 nếu có giá trị
                            if ($fetch_detail['style_2'] != '0') {
                                echo ', ' . trim($fetch_detail['style_2']);
                            }
                            
                            // Kiểm tra và hiển thị style_3 nếu có giá trị
                            if ($fetch_detail['style_3'] != '0') {
                                echo ', ' . trim($fetch_detail['style_3']);
                            }
                        ?>
                    </p>

                    <p class="product-detail">Dịp thích hợp: 
                        <?php 
                            echo trim($fetch_detail['dip_1']);
                            // Kiểm tra và hiển thị style_2 nếu có giá trị
                            if ($fetch_detail['dip_2'] != '0') {
                                echo ', ' . trim($fetch_detail['dip_2']);
                            }
                            
                            // Kiểm tra và hiển thị style_3 nếu có giá trị
                            if ($fetch_detail['dip_3'] != '0') {
                                echo ', ' . trim($fetch_detail['dip_3']);
                            }
                        ?>
                    </p>
                    <p class="product-detail"><?= $fetch_detail['chitiet']; ?></p>
                    
                    <div class="soluong">
                        <div>
                            <p>Kích thước:
                               <!-- HTML -->
                                <input type="radio" id="sizeS" name="size" value="sizeS" class="checkbox" <?= ($fetch_products['sizeS'] <= 0) ? 'disabled' : '' ?>>
                                <label for="sizeS" class="checkbox-label <?= ($fetch_products['sizeS'] <= 0) ? 'out-of-stock' : '' ?>">S</label>

                                <input type="radio" id="sizeM" name="size" value="sizeM" class="checkbox" <?= ($fetch_products['sizeM'] <= 0) ? 'disabled' : '' ?>>
                                <label for="sizeM" class="checkbox-label <?= ($fetch_products['sizeM'] <= 0) ? 'out-of-stock' : '' ?>">M</label>

                                <input type="radio" id="sizeL" name="size" value="sizeL" class="checkbox" <?= ($fetch_products['sizeL'] <= 0) ? 'disabled' : '' ?>>
                                <label for="sizeL" class="checkbox-label <?= ($fetch_products['sizeL'] <= 0) ? 'out-of-stock' : '' ?>">L</label>

                                <input type="radio" id="sizeXL" name="size" value="sizeXL" class="checkbox" <?= ($fetch_products['sizeXL'] <= 0) ? 'disabled' : '' ?>>
                                <label for="sizeXL" class="checkbox-label <?= ($fetch_products['sizeXL'] <= 0) ? 'out-of-stock' : '' ?>">XL</label>

                                <input type="radio" id="freesize" name="size" value="freesize" class="checkbox" <?= ($fetch_products['freesize'] <= 0) ? 'disabled' : '' ?>>
                                <label for="freesize" class="checkbox-label <?= ($fetch_products['freesize'] <= 0) ? 'out-of-stock' : '' ?>">Free size</label>

                            </p> 
                        </div> 
                        <div class="number">
                            <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                            <input type="number" name="qty" require min="1" value="1" max="20" maxlength="2" class="qty box" style="padding: 0rem 1.5rem; font-size: 1.1rem; border-radius: 1.5rem;">
                            <div class="buy">
                                <button type="submit" name="add_to_cart" class="btn" style="font-size: 1rem; padding:10px 1rem;" onclick="return validateSizeAndQty()"> Thêm vào giỏ hàng</button>
                                
                                <!-- <a href="thongtinthanhtoan.php?get_id=<?= $fetch_products['sanpham_id']?>" class="btn" onclick="return validateSizeAndQty()">Mua ngay</a> -->
                            </div>
                        </div>
                    </div>
                </div> 
            </form>
        <?php
                    }
                }
            }
        ?>
        </div>
        <div class="overlay-modal"></div> <!-- Lớp phủ ở ngoài modal -->
        <div id="myModal" class="modal">
            <span class="close">&times;</span>
            <img id="imgModal" class="modal-content" src="" alt="Image">
        </div>

        <div class="box review">
            <?php 
            $count_reviews = $conn->prepare("SELECT COUNT(*) FROM danhgia WHERE sanpham_id = ?");
            $count_reviews->execute([$parameter]);
            $total_reviews = $count_reviews->fetchColumn();
            ?>
            <div class="box-header">
                <h4>ĐÁNH GIÁ SẢN PHẨM (<?= $total_reviews; ?>)</h4>
                <div class="button-container">
                    <!-- <button class="filter-button" data-star="0">Tất cả</button>
                    <button class="filter-button" data-star="5">5 sao</button>
                    <button class="filter-button" data-star="4">4 sao</button>
                    <button class="filter-button" data-star="3">3 sao</button>
                    <button class="filter-button" data-star="2">2 sao</button>
                    <button class="filter-button" data-star="1">1 sao</button> -->
                </div>
            </div>
            
            <div class="reviews">
            <?php
                // Lấy đánh giá sản phẩm theo sanpham_id
                $select_reviews = $conn->prepare("SELECT * FROM danhgia WHERE sanpham_id = ? ORDER BY ngaygui  DESC LIMIT 3");
                $select_reviews->execute([$parameter]);

                $reviews = $select_reviews->fetchAll(PDO::FETCH_ASSOC);
                if (count($reviews) > 0) {
                    foreach ($reviews as $fetch_review) {
                        $rating = htmlspecialchars($fetch_review['sao']);
                        $user_id = htmlspecialchars($fetch_review['user_id']);
                        $hiddenUserId = substr($user_id, 0, 1) . '*****' . substr($user_id, -1);

                        // Lấy tất cả kích thước cho hoadon_id và sanpham_id từ bảng hoadon
                        $select_sizes = $conn->prepare("SELECT size FROM hoadon WHERE hoadon_id = ? AND sanpham_id = ?");
                        $select_sizes->execute([$fetch_review['hoadon_id'], $fetch_review['sanpham_id']]);
                        $sizes = $select_sizes->fetchAll(PDO::FETCH_COLUMN);

                        // Hiển thị tất cả kích thước
                        $sizeDisplay = !empty($sizes) ? implode(', ', $sizes) : 'Không có kích thước';
                        ?>
                        <div class="review" data-rating="<?= $rating; ?>">
                            <div class="user-id">
                                <p class="user-icon"><i class="bi bi-person-circle"></i></p>
                                <p class="id"><?= $hiddenUserId; ?></p>
                            </div>
                            <div class="stars">
                                <p class="star-rating">
                                    <?php
                                    for ($i = 0; $i < 5; $i++) {
                                        if ($i < $rating) {
                                            echo '<i class="bi bi-star-fill" style="font-size: .9rem; color: gold;"></i>';
                                        } else {
                                            echo '<i class="bi bi-star" style="font-size: .9rem; color: gold;"></i>';
                                        }
                                    }
                                    ?>
                                </p>
                                <p class="phanloai"><?= date('d/m/Y', strtotime($fetch_review['ngaygui'])); ?> | Phân loại hàng: <?= $sizeDisplay; ?></p>
                            </div>
                            <div class="noidung">
                                <p><?= htmlspecialchars($fetch_review['noidung']); ?></p>
                                <?php 
                                    if ($fetch_review['phanhoi']){
                                ?>
                                <p class="reply"><strong>Phản hồi từ người bán:</strong> <?= htmlspecialchars($fetch_review['phanhoi']); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <hr style="border: 1px solid #ccc;">
                        <?php
                    }?>  
            </div>
            <div class="show-cmt">
                <a href="javascript:void(0);" class="cmt" onclick="showComments()">Xem thêm</a>
            </div>
            <?php
            }else {
                echo '<p>Chưa có đánh giá nào cho sản phẩm này.</p>';
            }
        
            ?>
        </div>
        <div id="overlay" style="display: none;"></div>

        <div class="box review" id="show_cmt" style="display: none;">
            <?php 
            $count_reviews = $conn->prepare("SELECT COUNT(*) FROM danhgia WHERE sanpham_id = ?");
            $count_reviews->execute([$parameter]);
            $total_reviews = $count_reviews->fetchColumn();
            ?>
            <div class="box-header">
                <h4>ĐÁNH GIÁ SẢN PHẨM (<?= $total_reviews; ?>)</h4>
            </div>
            
            <div class="reviews">
            <?php
                // Lấy đánh giá sản phẩm theo sanpham_id
                $select_reviews = $conn->prepare("SELECT * FROM danhgia WHERE sanpham_id = ? ORDER BY ngaygui  DESC");
                $select_reviews->execute([$parameter]);

                $reviews = $select_reviews->fetchAll(PDO::FETCH_ASSOC);
                if (count($reviews) > 0) {
                    foreach ($reviews as $fetch_review) {
                        $rating = htmlspecialchars($fetch_review['sao']);
                        $user_id = htmlspecialchars($fetch_review['user_id']);
                        $hiddenUserId = substr($user_id, 0, 1) . '*****' . substr($user_id, -1);

                        // Lấy tất cả kích thước cho hoadon_id và sanpham_id từ bảng hoadon
                        $select_sizes = $conn->prepare("SELECT size FROM hoadon WHERE hoadon_id = ? AND sanpham_id = ?");
                        $select_sizes->execute([$fetch_review['hoadon_id'], $fetch_review['sanpham_id']]);
                        $sizes = $select_sizes->fetchAll(PDO::FETCH_COLUMN);

                        // Hiển thị tất cả kích thước
                        $sizeDisplay = !empty($sizes) ? implode(', ', $sizes) : 'Không có kích thước';
                        ?>
                        <div class="review" data-rating="<?= $rating; ?>">
                            <div class="user-id">
                                <p class="user-icon"><i class="bi bi-person-circle"></i></p>
                                <p class="id"><?= $hiddenUserId; ?></p>
                            </div>
                            <div class="stars">
                                <p class="star-rating">
                                    <?php
                                    for ($i = 0; $i < 5; $i++) {
                                        if ($i < $rating) {
                                            echo '<i class="bi bi-star-fill" style="font-size: .9rem; color: gold;"></i>';
                                        } else {
                                            echo '<i class="bi bi-star" style="font-size: .9rem; color: gold;"></i>';
                                        }
                                    }
                                    ?>
                                </p>
                                <p class="phanloai"><?= date('d/m/Y', strtotime($fetch_review['ngaygui'])); ?> | Phân loại hàng: <?= $sizeDisplay; ?></p>
                            </div>
                            <div class="noidung">
                                <p><?= htmlspecialchars($fetch_review['noidung']); ?></p>
                                <?php 
                                    if ($fetch_review['phanhoi']){
                                ?>
                                <p class="reply"><strong>Phản hồi từ người bán:</strong> <?= htmlspecialchars($fetch_review['phanhoi']); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <hr style="border: 1px solid #ccc;">
                        <?php
                    }
                } else {
                    echo '<p>Chưa có đánh giá nào cho sản phẩm này.</p>';
                }
            ?>  
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showComments() {
            var commentsBox = document.getElementById('show_cmt');
            var overlay = document.getElementById('overlay');
            
            if (commentsBox.style.display === 'none' || commentsBox.style.display === '') {
                commentsBox.style.display = 'block'; // Hiển thị box
                overlay.style.display = 'block'; // Hiển thị lớp phủ
            } else {
                commentsBox.style.display = 'none'; // Ẩn box nếu đã hiển thị
                overlay.style.display = 'none'; // Ẩn lớp phủ
            }
        }

        // Thêm sự kiện click cho lớp phủ
        document.getElementById('overlay').onclick = function() {
            document.getElementById('show_cmt').style.display = 'none'; // Ẩn box
            this.style.display = 'none'; // Ẩn lớp phủ
        }

    </script>
    
    <script src="js/user_script.js"></script>   
    <script>
        function validateSizeAndQty() {
        var sizeSelected = document.querySelector('input[name="size"]:checked');

        // Kiểm tra xem kích thước đã được chọn hay chưa
        if (!sizeSelected) {
            alert("Vui lòng chọn kích thước sản phẩm.");
            return false; // Ngăn việc chuyển đến trang vận chuyển
        }

        // Nếu tất cả các điều kiện đều được đáp ứng, cho phép chuyển đến trang vận chuyển
        return true;
    }

    </script>
    <script>
        // Lấy tất cả các thẻ hình ảnh thu nhỏ
        const thumbs = document.querySelectorAll('.thumb-list img');
        const mainImage = document.querySelector('#mainImage');

        // Lặp qua từng hình ảnh thu nhỏ và thêm sự kiện click
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                // Thay đổi hình ảnh chính thành đường dẫn của hình ảnh thu nhỏ được nhấp vào
                mainImage.src = thumb.src;
            });
        });
    </script>
    <script>
        const modal = document.getElementById("myModal");
        const modalImg = document.getElementById("imgModal");
        const overlay = document.querySelector(".overlay-modal");
        const figures = document.querySelectorAll(".img-box figure");

        // Khi nhấp vào một figure, hiển thị modal và lớp phủ
        figures.forEach(figure => {
            figure.addEventListener("click", () => {
                const img = figure.querySelector("img"); // Lấy thẻ <img> bên trong figure
                modal.classList.add("show"); // Hiển thị modal
                overlay.classList.add("show"); // Hiển thị lớp phủ
                modalImg.src = img.src; // Gán đường dẫn hình ảnh vào modal
            });
        });

        // Đóng modal khi nhấp vào nút đóng (X)
        document.querySelector(".close").addEventListener("click", () => {
            modal.classList.remove("show"); // Ẩn modal
            overlay.classList.remove("show"); // Ẩn lớp phủ
        });

    </script>

    <script>
        const img = document.querySelector('.view_page .img-box figure img');

        img.addEventListener('mousemove', function(e) {
            const rect = img.getBoundingClientRect();
            const x = e.clientX - rect.left;  // Vị trí x của con trỏ
            const y = e.clientY - rect.top;   // Vị trí y của con trỏ
            const xPercent = x / rect.width * 100;
            const yPercent = y / rect.height * 100;

            img.style.transformOrigin = `${xPercent}% ${yPercent}%`;  // Thiết lập vị trí phóng to theo con trỏ
            img.style.transform = 'scale(3)';  // Phóng to ảnh
        });

        img.addEventListener('mouseleave', function() {
            img.style.transform = 'scale(1)';  // Đặt lại scale về mặc định khi không còn hover
        });
    </script>
    <div class="products-new" style="margin-top: -5rem;">
        <div class="heading">
            <h1 style="margin-bottom: -2rem;">Có thể bạn cũng thích</h1> 
        </div>
        <?php include 'component/shop.php'; ?>
    </div> 
    <script src="js/sweetalert.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
    <!-- all_reviews.php?pid= -->
</body>
</html>