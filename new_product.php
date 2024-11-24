<div class="box-container" style="margin-top: -2rem; margin-bottom: 6rem;">
    <?php 
        $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE trangthai=? ORDER BY ngaynhapkho DESC LIMIT 10");
        $select_products->execute(['Đang hoạt động']);

        if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {

                // Lấy số lượng từ các cỡ sản phẩm và cộng lại với nhau
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
                            if (mb_strlen($product_name) > 30) {
                                // Nếu tên sản phẩm dài hơn 30 ký tự, hiển thị chỉ 30 ký tự và thêm ba dấu chấm ở cuối
                                $product_name = mb_substr($product_name, 0, 28).'...';
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
                    <p>Chưa có sản phẩm nào được thêm vào.</p>
                </div>
            ';
        }
    ?>
</div>
