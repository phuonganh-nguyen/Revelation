<div class="box-container" style="margin-top: -2rem; margin-bottom: 6rem;">
    <?php 
        // Kiểm tra nếu có user_id (Giả sử bạn đã có $user_id từ đâu đó)
        if (isset($user_id)) {
            // Kiểm tra xem user_id có tồn tại trong bảng user_preferences hay không
            $check_preferences = $conn->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
            $check_preferences->execute([$user_id]);

            if ($check_preferences->rowCount() > 0) {
                // Nếu có, lấy ra các sanpham_id từ bảng recommended_products
                $select_recommended = $conn->prepare("SELECT sanpham_id FROM recommended_products WHERE user_id = ? ORDER BY similarity DESC LIMIT 10");
                $select_recommended->execute([$user_id]);

                $recommended_products = $select_recommended->fetchAll(PDO::FETCH_COLUMN);

                // Nếu có sản phẩm gợi ý, lấy thông tin từ bảng sanpham
                if (count($recommended_products) > 0) {
                    // Tạo câu lệnh IN động với các sanpham_id
                    $placeholders = implode(',', array_fill(0, count($recommended_products), '?'));
                    
                    // Thêm trạng thái "Đang hoạt động" vào cuối
                    $sql = "SELECT * FROM sanpham WHERE sanpham_id IN ($placeholders) AND trangthai = ? ORDER BY luotmua DESC LIMIT 10";
                    $select_products = $conn->prepare($sql);

                    // Truyền tham số vào execute, bao gồm cả các sanpham_id và trạng thái
                    $params = array_merge($recommended_products, ['Đang hoạt động']);
                    $select_products->execute($params);

                    if ($select_products->rowCount() > 0) {
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                            // Tính tổng số lượng từ các cỡ sản phẩm
                            $total_quantity = $fetch_products['sizeS'] + $fetch_products['sizeM'] + $fetch_products['sizeL'] + $fetch_products['sizeXL'] + $fetch_products['freesize'];

                            // Kiểm tra tổng số lượng, nếu = 0 thì không hiển thị sản phẩm
                            if ($total_quantity > 0 && !empty($fetch_products['old_price'])) {
    ?>
                            <form action="" method="post" class="box" onclick="window.location.href='view_page.php?pid=<?= $fetch_products['sanpham_id']?>'">
                                <img src="uploaded_files/<?= $fetch_products['image'];?>" class="image">

                                <?php
                                    // Kiểm tra trạng thái dựa trên tổng số lượng
                                    if ($total_quantity > 0 && $total_quantity <= 5) {
                                        echo '<span class="soluong" style="color: red;">Chỉ còn ' . $total_quantity . ' sản phẩm</span>';
                                    }
                                ?>
                                <div class="content">
                                    <div class="button">
                                        <?php
                                            $product_name = $fetch_products['name']; // Lấy tên sản phẩm từ dữ liệu

                                            // Kiểm tra độ dài của tên sản phẩm
                                            if (mb_strlen($product_name) > 24) {
                                                // Nếu tên sản phẩm dài hơn 30 ký tự, hiển thị chỉ 30 ký tự và thêm ba dấu chấm ở cuối
                                                $product_name = mb_substr($product_name, 0, 24).'...';
                                            }
                                        ?>
                                        <div><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $product_name ?></a></div>
                                    </div>
                                    <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.') ?> VNĐ</p>
                                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                                </div>
                            </form>
    <?php
                            } // Kết thúc if kiểm tra tổng số lượng
                        }
                    } else {
                        echo '
                            <div class="empty">
                                <p>Không có sản phẩm phù hợp với sở thích của bạn.</p>
                            </div>
                        ';
                    }
                } else {
                    echo '
                        <div class="empty">
                            <p>Không có sản phẩm được gợi ý cho bạn.</p>
                        </div>
                    ';
                }
            } else {
                // Nếu không có preferences, lấy sản phẩm như đoạn mã ban đầu
                $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE trangthai=? ORDER BY luotmua DESC LIMIT 10");
                $select_products->execute(['Đang hoạt động']);

                if ($select_products->rowCount() > 0) {
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                        // Tính tổng số lượng từ các cỡ sản phẩm
                        $total_quantity = $fetch_products['sizeS'] + $fetch_products['sizeM'] + $fetch_products['sizeL'] + $fetch_products['sizeXL'] + $fetch_products['freesize'];

                        // Kiểm tra tổng số lượng, nếu = 0 thì không hiển thị sản phẩm
                        if ($total_quantity > 0 && !empty($fetch_products['old_price'])) {
    ?>
                            <form action="" method="post" class="box" onclick="window.location.href='view_page.php?pid=<?= $fetch_products['sanpham_id']?>'">
                                <img src="uploaded_files/<?= $fetch_products['image'];?>" class="image">

                                <?php
                                    if ($total_quantity > 0 && $total_quantity <= 5) {
                                        echo '<span class="soluong" style="color: red;">Chỉ còn ' . $total_quantity . ' sản phẩm</span>';
                                    }
                                ?>
                                <div class="content">
                                    <div class="button">
                                        <?php
                                            $product_name = $fetch_products['name'];
                                            if (mb_strlen($product_name) > 24) {
                                                $product_name = mb_substr($product_name, 0, 24).'...';
                                            }
                                        ?>
                                        <div><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $product_name ?></a></div>
                                    </div>
                                    <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.') ?> VNĐ</p>
                                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                                </div>
                            </form>
    <?php
                        }
                    }
                } else {
                    echo '
                        <div class="empty">
                            <p>Chưa có sản phẩm nào được thêm vào.</p>
                        </div>
                    ';
                }
            }
        } else {
            // Nếu không có user_id, lấy sản phẩm như đoạn mã ban đầu
            $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE trangthai=? ORDER BY luotmua DESC LIMIT 10");
            $select_products->execute(['Đang hoạt động']);

            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                    // Tính tổng số lượng từ các cỡ sản phẩm
                    $total_quantity = $fetch_products['sizeS'] + $fetch_products['sizeM'] + $fetch_products['sizeL'] + $fetch_products['sizeXL'] + $fetch_products['freesize'];

                    // Kiểm tra tổng số lượng, nếu = 0 thì không hiển thị sản phẩm
                    if ($total_quantity > 0 && !empty($fetch_products['old_price'])) {
    ?>
                            <form action="" method="post" class="box" onclick="window.location.href='view_page.php?pid=<?= $fetch_products['sanpham_id']?>'">
                                <img src="uploaded_files/<?= $fetch_products['image'];?>" class="image">

                                <?php
                                    if ($total_quantity > 0 && $total_quantity <= 5) {
                                        echo '<span class="soluong" style="color: red;">Chỉ còn ' . $total_quantity . ' sản phẩm</span>';
                                    }
                                ?>
                                <div class="content">
                                    <div class="button">
                                        <?php
                                            $product_name = $fetch_products['name'];
                                            if (mb_strlen($product_name) > 24) {
                                                $product_name = mb_substr($product_name, 0, 24).'...';
                                            }
                                        ?>
                                        <div><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $product_name ?></a></div>
                                    </div>
                                    <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.') ?> VNĐ</p>
                                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                                </div>
                            </form>
    <?php
                    }
                }
            } else {
                echo '
                    <div class="empty">
                        <p>Chưa có sản phẩm nào được thêm vào.</p>
                    </div>
                ';
            }
        }
    ?>
</div>
