<?php 
    if (isset($_POST['add_to_cart'])) {
        if ($user_id != '') {
            $id = sp_id();
            $product_id = $_POST['product_id'];
            $qty = $_POST['qty'];

            $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=? AND sanpham_id=?");
            $verify_cart->execute([$user_id, $product_id]);

            $max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
            $max_cart_items->execute([$user_id]);

            // Verify product availability
            $check_stock = $conn->prepare("SELECT soluong FROM `sanpham` WHERE sanpham_id=?");
            $check_stock->execute([$product_id]);
            $available_stock = $check_stock->fetch(PDO::FETCH_ASSOC)['soluong'];

            if ($verify_cart->rowCount() > 0){
                $warning_msg[] = 'Sản phẩm đã tồn tại trong giỏ hàng';
            } else if ($max_cart_items->rowCount() > 20){
                $warning_msg[] = 'Giỏ hàng đã đầy';
            } else if ($qty > $available_stock){
                $warning_msg[] = 'Vượt quá số lượng sản phẩm có sẵn';
            } else {
                $select_price = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=? LIMIT 1");
                $select_price->execute([$product_id]);
                $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

                $insert_cart = $conn->prepare("INSERT INTO `cart`(cart_id, user_id, sanpham_id, price, qty) VALUES(?,?,?,?,?)");
                $insert_cart->execute([$id, $user_id, $product_id, $fetch_price['price'], $qty]);
                $update_product_quantity = $conn->prepare("UPDATE `sanpham` SET soluong = soluong - ? WHERE sanpham_id = ?");
                $update_product_quantity->execute([$qty, $product_id]);
                $success_msg[] = 'Đã thêm vào giỏ hàng';
            }

        } else{
            $warning_msg[] = 'Vui lòng đăng nhập!';
        }
    }
    
?>