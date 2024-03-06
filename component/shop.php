
        <div class="box-container">
        <?php
            $select_suggested_products = $conn->prepare("SELECT * FROM `sanpham` WHERE loai_sp=? AND trangthai=? AND sanpham_id<>? LIMIT 6");
            $select_suggested_products->execute([$category, 'Đang hoạt động', $pid]);

            while ($fetch_suggested_products = $select_suggested_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <form action="" method="post" class="box <?php if($fetch_suggested_products['soluong'] == 0){echo "disabled";} ?>">
                    <img src="uploaded_files/<?= $fetch_suggested_products['image'];?>" class="image">
                    <?php if($fetch_suggested_products['soluong'] > 9) {?>
                        <span class="soluong" style="color: green;">Có sẵn</span>
                    <?php } elseif($fetch_suggested_products['soluong'] == 0) {?>
                        <span class="soluong" style="color: red;">Hết hàng</span>
                    <?php } else {?>
                        <span class="soluong" style="color: red;">Chỉ còn <?= $fetch_suggested_products['soluong']; ?> sản phẩm</span>
                    <?php }?>
                    <div class="content">
                        <!-- <img src="" alt=""> -->
                        <div class="button">
                            <div><a href="view_page.php?pid=<?= $fetch_suggested_products['sanpham_id']?>" class="name"><?= $fetch_suggested_products['name']?></a></div>
                            <div>
                                <button type="submit" name="add_to_cart"> <i class="fa-solid fa-cart-plus"></i></button>
                                <button type="submit" name="add_to_wishlist"><i class="fa-solid fa-heart-circle-plus"></i></button>
                                
                            </div>
                        </div>
                        <p class="price"><?= $fetch_suggested_products['price']; ?>VNĐ</p>
                        <input type="hidden" name="product_id" value="<?= $fetch_suggested_products['sanpham_id']?>">
                        <div class="flex-btn">
                            <a href="checkout.php?get_id=<?= $fetch_suggested_products['sanpham_id']?>" class="btn" style="color: var(pi); padding-top:8px;">Mua ngay</a>
                            <input type="number" name="qty" require min="1" value="1" max="99" maxlength="2" class="qty box">
                        </div>
                    </div>
            </form>
            <?php
            }
            ?>
        </div>
    </div>
