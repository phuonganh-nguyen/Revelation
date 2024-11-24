<?php 
// Bao gồm một tệp PHP khác vào tệp hiện tại
include '../component/connect.php';

if (isset($_COOKIE['kho_id'])) {
    $user_id = $_COOKIE['kho_id'];
} else {
    $user_id = '';
    header('location:../login.php');
}

if (isset($_POST['enter'])) {
    $product_id = $_POST['product_id'];
    $nhacungcap = $_POST['nhacungcap'];
    $soluongS = (int)$_POST['quantityS']; // Chuyển đổi thành số nguyên
    $soluongM = (int)$_POST['quantityM']; // Chuyển đổi thành số nguyên
    $soluongL = (int)$_POST['quantityL']; // Chuyển đổi thành số nguyên
    $soluongXL = (int)$_POST['quantityXL']; // Chuyển đổi thành số nguyên
    $freesize = (int)$_POST['freesize']; // Chuyển đổi thành số nguyên

    $old_price = (float)$_POST['old_price'];  // Giá vốn
    $ori_price = (float)$_POST['ori_price'];  // Giá bán
    $ngaynhap = date('Y-m-d');

    // Thêm thông tin vào bảng nhapkho
    $insert_nhapkho = $conn->prepare("INSERT INTO `nhapkho` (sanpham_id, admin_id, sizeS, sizeM, sizeL, sizeXL, freesize, ngaynhap) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_nhapkho->execute([$product_id, $user_id, $soluongS, $soluongM, $soluongL, $soluongXL, $freesize, $ngaynhap]);

    // Lấy số lượng hiện tại từ bảng sanpham
    $select_current_qty = $conn->prepare("SELECT sizeS, sizeM, sizeL, sizeXL, freesize FROM `sanpham` WHERE sanpham_id = ?");
    $select_current_qty->execute([$product_id]);
    $current_qty = $select_current_qty->fetch(PDO::FETCH_ASSOC); // Lấy số lượng hiện tại từ bảng sanpham

    // Cộng số lượng vào số lượng hiện tại, nếu cột không có giá trị thì khởi tạo là 0
    $new_qtyS = ((int)$current_qty['sizeS'] ?? 0) + $soluongS; // Chuyển đổi sang số nguyên
    $new_qtyM = ((int)$current_qty['sizeM'] ?? 0) + $soluongM; // Chuyển đổi sang số nguyên
    $new_qtyL = ((int)$current_qty['sizeL'] ?? 0) + $soluongL; // Chuyển đổi sang số nguyên
    $new_qtyXL = ((int)$current_qty['sizeXL'] ?? 0) + $soluongXL; // Chuyển đổi sang số nguyên
    $new_freesize = ((int)$current_qty['freesize'] ?? 0) + $freesize; // Chuyển đổi sang số nguyên

    // Cập nhật số lượng tương ứng trong bảng sanpham
    $update_product = $conn->prepare("UPDATE `sanpham` SET sizeS = ?, sizeM = ?, sizeL = ?, sizeXL = ?, freesize = ? WHERE sanpham_id = ?");
    $update_product->execute([$new_qtyS, $new_qtyM, $new_qtyL, $new_qtyXL, $new_freesize, $product_id]);

    // Cập nhật giá vốn và giá bán trong bảng sanpham
    $update_prices = $conn->prepare("UPDATE `sanpham` SET old_price = ?, price = ?, ngaynhapkho = ? WHERE sanpham_id = ?");
    $update_prices->execute([$old_price, $ori_price, $ngaynhap, $product_id]);

    $success_msg[] = 'Sản phẩm đã được cập nhật';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Storekeeper page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>  
    <div class="main-container">
        <?php include '../component/stock_header.php'; ?>
        <section class="post-editor"> 
            <div class="back">
                <a href="all_stock.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
            <div class="heading">
                <h1>Nhập kho</h1>
            </div>
            <div class="box-container">
                <?php
                    $product_id = $_GET['post_id'];
                    $select_product = $conn->prepare("SELECT * FROM `sanpham` WHERE sanpham_id=?");
                    $select_product->execute([$product_id]);
                    if($select_product->rowCount() > 0) {
                        while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)) {
                            // Lấy giá gần đây nhất từ bảng sanpham
                            $get_last_price = $conn->prepare("SELECT old_price, price FROM `sanpham` WHERE sanpham_id=?");
                            $get_last_price->execute([$product_id]);
                            $last_price = $get_last_price->fetch(PDO::FETCH_ASSOC);
                            // Lấy thông tin từ bảng nhapkho
                            $get_stock_info = $conn->prepare("SELECT sizeS, sizeM, sizeL, sizeXL, freesize, ngaynhap, admin_id FROM `nhapkho` WHERE sanpham_id = ?");
                            $get_stock_info->execute([$product_id]);
                            $stock_info = $get_stock_info->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div class="form-container">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                        <div class="input-field">
                            <p style="text-transform: uppercase; color: #851639;"><?= $fetch_product['name'];?></p>
                        </div>
                        <p>Kích cỡ & số lượng<span>*</span></p>
                        <div class="flex">  
                            <div class="box">
                                <div class="input-field">
                                    <p>Size S</p>
                                    <input type="number" name="quantityS" maxlength="100" class="box">
                                </div>
                                <div class="input-field">
                                    <p>Size M</p>
                                    <input type="number" name="quantityM" maxlength="100" class="box">                                 
                                </div>
                                <div class="input-field">
                                    <p>Size L</p>
                                    <input type="number" name="quantityL" maxlength="100" class="box">                                 
                                </div>
                            </div>
                            <div class="box">
                                <div class="input-field">
                                    <p>Size XL</p>
                                    <input type="number" name="quantityXL" maxlength="100" class="box">                                 
                                </div>
                                <div class="input-field">
                                    <p>Free size</p>
                                    <input type="number" name="freesize" maxlength="100" class="box">                                 
                                </div>
                            </div>
                        </div>
                        <div class="input-field">
                            <p>Nhà cung cấp<span>*</span></p>
                            <select name="nhacungcap" style="text-transform: capitalize;"> 
                                <option value="Xưởng Song Luân" style="text-transform: capitalize;">Xưởng Song Luân</option>
                                <option value="Xưởng Đăng Đoo" style="text-transform: capitalize;">Xưởng Đăng Đoo</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Giá vốn<span>*</span></p>
                            <input type="number" name="old_price" value="<?= $last_price ? $last_price['old_price'] : ''; ?>" class="box" required>
                        </div>
                        <div class="input-field">
                            <p>Giá bán<span>*</span></p>
                            <input type="number" name="ori_price" value="<?= $last_price ? $last_price['price'] : ''; ?>" class="box" required>
                        </div>
                        <div class="flex-btn">
                            <input type="submit" name="enter" value="Nhập kho" class="btn">
                        </div>
                        <div class="stock-info">
                            <h3>Số lượng hiện tại còn trong kho</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Size S</th>
                                        <th>Size M</th>
                                        <th>Size L</th>
                                        <th>Size XL</th>
                                        <th>Free Size</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= $fetch_product['sizeS'] ?></td>
                                        <td><?= $fetch_product['sizeM'] ?></td>
                                        <td><?= $fetch_product['sizeL'] ?></td>
                                        <td><?= $fetch_product['sizeXL'] ?></td>
                                        <td><?= $fetch_product['freesize'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        // Sắp xếp mảng stock_info theo ngày nhập (ngày mới nhất sẽ ở trên)
                        usort($stock_info, function ($a, $b) {
                            return strtotime($b['ngaynhap']) - strtotime($a['ngaynhap']);
                        });
                        ?>
                        <div class="stock-info">
                            <h3>Thông tin nhập kho</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ngày nhập</th>
                                        <th>Size S</th>
                                        <th>Size M</th>
                                        <th>Size L</th>
                                        <th>Size XL</th>
                                        <th>Free Size</th>
                                        <th>Nhân viên</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stock_info as $stock): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($stock['ngaynhap'])) ?></td>
                                            <td><?= $stock['sizeS'] ?></td>
                                            <td><?= $stock['sizeM'] ?></td>
                                            <td><?= $stock['sizeL'] ?></td>
                                            <td><?= $stock['sizeXL'] ?></td>
                                            <td><?= $stock['freesize'] ?></td>
                                            <?php 
                                                $admin_id = $stock['admin_id'];
                                                $select_admin = $conn->prepare("SELECT * FROM `user` WHERE user_id=?");
                                                $select_admin->execute([$admin_id]); 
                                                $fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);?>
                                            <td><?= $fetch_admin['name'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <?php 
                        }
                    }
                ?>
            </div>
        </section>
    </div>
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>
