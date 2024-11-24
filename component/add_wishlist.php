<?php 
    if (isset($_POST['add_to_wishlist'])) {
        if ($user_id != '') {
            // $id = sp_id();
            $product_id = $_POST['product_id'];
            $qty = $_POST['qty'];

            $verify_wish = $conn->prepare("SELECT * FROM `love` WHERE user_id=? AND sanpham_id=?");
            $verify_wish->execute([$user_id, $product_id]);

            if ($verify_wish->rowCount() > 0){
                $warning_msg[] = 'Sản phẩm đã tồn tại trong mục yêu thích';
            } else if ($user_id != '') {
                $select_price = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
                $select_price->execute([$product_id]);
                $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

                $insert_wish = $conn->prepare("INSERT INTO `love`(user_id, sanpham_id, price) VALUES(?,?,?)");
                $insert_wish->execute([$user_id, $product_id, $fetch_price['price']]); 
                // Cộng thêm 1 vào cột yeuthich trong bảng sanpham
                $increment_favorite_count = $conn->prepare("UPDATE `sanpham` SET luotthich = luotthich + 1 WHERE sanpham_id = ?");
                $increment_favorite_count->execute([$product_id]);
                $success_msg[] = 'Thêm thành công vào mục yêu thích!';
            }

        } else{
            $warning_msg[] = 'Vui lòng đăng nhập!';
        }
    }
    
?>