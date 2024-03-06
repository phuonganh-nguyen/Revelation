<?php 
    if (isset($_POST['add_to_wishlist'])) {
        if ($user_id != '') {
            $id = sp_id();
            $product_id = $_POST['product_id'];
            $qty = $_POST['qty'];

            $verify_wish = $conn->prepare("SELECT * FROM `yeuthich` WHERE user_id=? AND sanpham_id=?");
            $verify_wish->execute([$user_id, $product_id]);

            if ($verify_wish->rowCount() > 0){
                $warning_msg[] = 'Sản phẩm đã tồn tại trong mục yêu thích';
            } else if ($user_id != '') {
                $select_price = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
                $select_price->execute([$product_id]);
                $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

                $insert_wish = $conn->prepare("INSERT INTO `yeuthich`(love_id, user_id, sanpham_id, price) VALUES(?,?,?,?)");
                $insert_wish->execute([$id, $user_id, $product_id, $fetch_price['price']]); 
                $success_msg[] = 'Thêm thành công vào mục yêu thích!';
            }

        } else{
            $warning_msg[] = 'Vui lòng đăng nhập!';
        }
    }
    
?>