
<div class="box-container" style="margin-bottom: 8rem;">
            <?php 
                $select_products = $conn->prepare("SELECT * FROM `sanpham` WHERE trangthai=? ORDER BY ngaytao DESC LIMIT 8");
                $select_products->execute(['Đang hoạt động']);

                if ($select_products->rowCount() > 0) {
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    
            ?>
            <form action="" method="post" class="box <?php if($fetch_products['soluong'] == 0){echo "disabled";} ?>">
                <img src="uploaded_files/<?= $fetch_products['image'];?>" class="image">
                <?php if($fetch_products['soluong'] > 9) {?>
                    <span class="soluong" style="color: green;">Có sẵn</span>
                <?php } elseif($fetch_products['soluong'] == 0) {?>
                    <span class="soluong" style="color: red;">Hết hàng</span>
                <?php } else {?>
                    <span class="soluong" style="color: red;">Chỉ còn <?= $fetch_products['soluong']; ?> sản phẩm</span>
                <?php }?>
                <div class="content">
                    <!-- <img src="" alt=""> -->
                    <div class="button"> 
                        <div><a href="view_page.php?pid=<?= $fetch_products['sanpham_id']?>" class="name"><?= $fetch_products['name']?></a></div>
                        <div>
                            <button type="submit" name="add_to_cart"> <i class="fa-solid fa-cart-plus"></i></button>
                            <button type="submit" name="add_to_wishlist"><i class="fa-solid fa-heart-circle-plus"></i></button>
                            
                        </div>
                    </div>
                    <p class="price"><?= $fetch_products['price']; ?>VNĐ</p>
                    <input type="hidden" name="product_id" value="<?= $fetch_products['sanpham_id']?>">
                    <div class="flex-btn">
                        <a href="checkout.php?get_id=<?= $fetch_products['sanpham_id']?>" class="btn" style="color: var(pi); padding-top:8px;">Mua ngay</a>
                        <input type="number" name="qty" require min="1" value="1" max="99" maxlength="2" class="qty box">
                    </div>
                </div>
            </form>
        <?php
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
    <div class="categories">        
        <div class="">
            <div class="box" style="margin-left: 75%; margin-top: -20rem;">
                <a href="menu.php" class="btn">Xem Tất Cả</a>
            </div>
            </div>
    </div>
    
