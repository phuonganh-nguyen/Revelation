<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
include '../component/connect.php';
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:../login.php');
}

    // Xóa bộ sưu tập
    if (isset($_POST['delete_collection'])) {
        $collection_id = $_POST['collection_id'];

        // Xóa sản phẩm liên quan trong bảng collection
        $delete_collection_items = $conn->prepare("DELETE FROM `collection` WHERE name = (SELECT name FROM `anhbst` WHERE id = ?)");
        $delete_collection_items->execute([$collection_id]);

        // Xóa bộ sưu tập khỏi bảng anhbst
        $delete_collection = $conn->prepare("DELETE FROM `anhbst` WHERE id = ?");
        $delete_collection->execute([$collection_id]);

        $success_msg[] = 'Bộ sưu tập và các sản phẩm liên quan đã được xóa thành công!';
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Collection display page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="acc-page">
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="show-post">
            <div class="heading" style="margin-bottom: 1rem;">
                <h1>Tất cả bộ sưu tập</h1>
                <!-- <img src="../images/justlogo2.png" width="120"> -->
            </div>

            <div class="box-container">
                <div class="box">
                    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align: left;">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên BST</th>
                                <th>Ngày tạo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $select_anhbst = $conn->prepare("SELECT * FROM `anhbst` ORDER BY ngaytao DESC");
                            $select_anhbst->execute();

                            if ($select_anhbst->rowCount() > 0) {
                                $stt = 1; // Khởi tạo số thứ tự
                                while ($fetch_anhbst = $select_anhbst->fetch(PDO::FETCH_ASSOC)){
                                    ?>
                                    <a href="#">
                                        <tr>
                                            <td><?= $stt++; ?></td> <!-- In số thứ tự -->
                                            <td style="text-transform: capitalize;">
                                                <a href="detail_collection.php?id=<?= $fetch_anhbst['id']; ?>"><?= htmlspecialchars($fetch_anhbst['name']); ?></a>
                                            </td>
                                            <td><?= $fetch_anhbst['ngaytao']; ?></td> <!-- Ngày tạo -->
                                            <td>
                                                <form action="" class="delete_bst" method="post" style="display: inline;">
                                                    <input type="hidden" name="collection_id" value="<?= $fetch_anhbst['id']; ?>">
                                                    <button type="submit" class="" name="delete_collection" onclick="return confirm('Bạn có chắc chắn muốn xóa bộ sưu tập này không?');">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    </a>
                                    <?php
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
            <div style="align-items: center; display: flex; justify-content: center;">
                <a href="add_collection.php" class="btn">Thêm Bộ sưu tập</a>
            </div>
        </section>
    </div>


    <!-- <script src="http://cdnjs.cloudflare.com/ajax.libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>