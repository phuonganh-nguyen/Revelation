<?php 
    if (isset($_POST['add_to_cart'])) {
        if ($user_id != '') {
            $id = sp_id();
            $product_id = $_POST['product_id'];
            $qty = $_POST['qty'];
            $size = $_POST['size']; // Lấy giá trị của radio button kích thước sản phẩm

            // Kiểm tra số lượng hàng trong giỏ hàng
            $max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
            $max_cart_items->execute([$user_id]);
            // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng hay chưa
            $check_cart_item = $conn->prepare("SELECT * FROM `cart` WHERE user_id=? AND sanpham_id=? AND size=?");
            $check_cart_item->execute([$user_id, $product_id, $size]);

            
            // Kiểm tra số lượng sản phẩm có sẵn
            $check_stock = $conn->prepare("SELECT $size FROM `sanpham` WHERE sanpham_id=?");
            $check_stock->execute([$product_id]);
            $available_stock = $check_stock->fetchColumn();

            $select_price = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id = ?");
            $select_price->execute([$product_id]);
            $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);
            $product_price = $fetch_price['price'];



            if ($max_cart_items->rowCount() > 20){
                $warning_msg[] = 'Giỏ hàng đã đầy';
            } elseif ($qty > $available_stock){
                $warning_msg[] = 'Vượt quá số lượng sản phẩm có sẵn';
            } else{
                if ($check_cart_item->rowCount() > 0) {
                    $warning_msg[] = 'Sản phẩm đã có trong giỏ hàng';
                } else {
                    // Chèn thông tin vào giỏ hàng, bao gồm cả kích thước sản phẩm
                    $insert_cart = $conn->prepare("INSERT INTO `cart`(cart_id, user_id, sanpham_id, size, qty, price) VALUES(?,?,?,?,?,?)");
                    $insert_cart->execute([$id, $user_id, $product_id, $size, $qty, $product_price]);
    
                    
                    // Cập nhật số lượng sản phẩm trong bảng sản phẩm
                    // $update_product_quantity = $conn->prepare("UPDATE `sanpham` SET $size = $size - ? WHERE sanpham_id = ?");
                    // $update_product_quantity->execute([$qty, $product_id]);
                    $success_msg[] = 'Đã thêm vào giỏ hàng';
                }
            }

        } else{
            $warning_msg[] = 'Vui lòng đăng nhập!';
        }
    }   
?>
