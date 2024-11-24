<?php 
include '../component/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:../login.php');
}

if (isset($_GET['id'])) {
    $collection_id = $_GET['id'];

    // Lấy thông tin bộ sưu tập từ bảng anhbst dựa trên ID
    $select_bst = $conn->prepare("SELECT * FROM `anhbst` WHERE id = ?");
    $select_bst->execute([$collection_id]);
    $bst = $select_bst->fetch(PDO::FETCH_ASSOC);
}

// Xóa sản phẩm khỏi collection
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    // Xóa sản phẩm khỏi bảng collection
    $delete_product = $conn->prepare("DELETE FROM `collection` WHERE sanpham_id = ? AND name = ?");
    $delete_product->execute([$product_id, $bst['name']]);

    $success_msg[] = 'Sản phẩm đã được xóa khỏi bộ sưu tập!';
}

// Cập nhật mô tả
$current_description = $bst['mota']; // Lưu mô tả hiện tại

if (isset($_POST['update_description'])) {
    $description = $_POST['description']; // Lấy mô tả mới
    $collection_name = $bst['name']; // Lấy tên bộ sưu tập

    // Cập nhật mô tả vào bảng anhbst
    $update_description = $conn->prepare("UPDATE `anhbst` SET mota = ? WHERE name = ?");
    $update_description->execute([$description, $collection_name]);

    $current_description = $description; // Cập nhật mô tả hiện tại
    $success_msg[] = 'Mô tả đã được cập nhật thành công!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Product display page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="storekeeper-page">
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="show-post">
            <div class="back">
                <a href="show_collection.php"><i class="bi bi-caret-left-fill"></i>Trở về</a>
            </div>
            <div class="heading" style="margin-bottom: 1rem;">
                <h1>Bộ sưu tập: <?= htmlspecialchars($bst['name']); ?></h1>
            </div>
            <!-- Mô tả bộ sưu tập -->
            <form action="" method="post" class="collection-description" style="margin-bottom: 1rem;">
                <textarea name="description" rows="4" cols="50" placeholder="Nhập mô tả cho bộ sưu tập"><?= htmlspecialchars($current_description); ?></textarea>
                <input type="hidden" name="collection_id" value="<?= $bst['id']; ?>"> <!-- ID của bộ sưu tập -->
                <button type="submit" class="btn" name="update_description">Cập nhật</button>
            </form>

            <?php
            // Lấy sanpham_id từ bảng collection dựa trên name của bộ sưu tập
            $select_collection = $conn->prepare("SELECT sanpham_id FROM `collection` WHERE name = ?");
            $select_collection->execute([$bst['name']]); // Truy vấn theo tên bộ sưu tập

            // Kiểm tra nếu có sản phẩm trong bộ sưu tập
            if ($select_collection->rowCount() > 0) {
                while ($collection_item = $select_collection->fetch(PDO::FETCH_ASSOC)) {
                    $product_id = $collection_item['sanpham_id'];

                    // Lấy tên sản phẩm từ bảng sanpham dựa trên sanpham_id
                    $select_product = $conn->prepare("SELECT name FROM `sanpham` WHERE sanpham_id = ?");
                    $select_product->execute([$product_id]);
                    $product = $select_product->fetch(PDO::FETCH_ASSOC);

                    if ($product) {
                        ?>
                        <form action="" method="post" class="product-stock" id="product-<?= $product_id; ?>" style="display: flex; justify-content: space-between; align-items: center;">
                            <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                            <span><?= htmlspecialchars($product['name']); ?></span> 
                            <button type="submit" name="delete_product" class="delete" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">Xóa</button>
                        </form>
                        <?php
                    }
                }
            } else {
                echo '<p>Chưa có sản phẩm trong bộ sưu tập này.</p>';
            }
            ?>

        </section>
    </div>

    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>
