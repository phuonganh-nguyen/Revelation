<?php 
// Bao gồm tệp PHP kết nối đến cơ sở dữ liệu
include '../component/connect.php';

// Kiểm tra người dùng đã đăng nhập chưa
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    header('location:../login.php');
    exit(); // Dừng thực thi mã sau khi chuyển hướng
}

// Kiểm tra nếu nút "Lưu" được nhấn
if (isset($_POST['publish'])) {
    // Lấy thông tin từ form
    $collection_name = $_POST['name'];
    $description = $_POST['description'];

    // Xử lý ảnh bộ sưu tập
    $image_tmp_name = $_FILES['background']['tmp_name'];
    $image_name = $_FILES['background']['name'];
    $image_new_name = uniqid('collection_') . '_' . $image_name;
    $image_path = '../uploaded_files/' . $image_new_name;
    move_uploaded_file($image_tmp_name, $image_path);

    // Khởi tạo mảng để lưu sản phẩm đã chọn
    $selected_products = [];

    // Lấy danh sách ID sản phẩm từ input ẩn
    if (isset($_POST['selected_products']) && !empty($_POST['selected_products'])) {
        // Chuyển đổi JSON thành mảng
        $selected_products = json_decode($_POST['selected_products'], true);
    }
    

    // Kiểm tra xem mảng sản phẩm đã chọn có tồn tại không
    if (!empty($selected_products)) {
        // Thêm bộ sưu tập vào bảng `collection` cho từng sản phẩm
        $insert_collection = $conn->prepare("INSERT INTO `collection` (name, sanpham_id) VALUES (?, ?)");

        // Sử dụng vòng lặp để thêm từng sản phẩm vào bảng
        foreach ($selected_products as $sanpham_id) {
            // Đảm bảo rằng ID sản phẩm không rỗng
            if (!empty($sanpham_id)) {
                $insert_collection->execute([$collection_name, $sanpham_id]);
            }
        }
    }

    // Thêm ảnh bộ sưu tập vào bảng `anhBST`
    $insert_image = $conn->prepare("INSERT INTO `anhBST` (name, image, mota) VALUES (?, ?, ?)");
    $insert_image->execute([$collection_name, $image_path, $description]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Add Collection Page</title>
    <link rel="shortcut icon" href="../images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    
    <div class="main-container">
        <?php include '../component/admin_header.php'; ?>
        <section class="post-editor"> 
            <div class="heading">
                <h1>Thêm bộ sưu tập</h1>
            </div>
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="register">
                    <div class="input-field">
                        <p>Tên bộ sưu tập<span>*</span></p>
                        <input type="text" name="name" maxlength="100" placeholder="Nhập tên BST" required class="box">
                    </div>

                    <?php
                        $select_product = $conn->query("SELECT * FROM sanpham");
                        $product = $select_product->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="input-field">
                        <p>Chọn sản phẩm<span>*</span></p>
                        <select name="product-select" id="product-select" onchange="addProductName()" style="text-transform: capitalize"> 
                            <?php foreach ($product as $sanpham): ?>
                                <option value="<?= $sanpham['sanpham_id']; ?>" style="text-transform: capitalize">
                                    <?= $sanpham['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <ul id="selected-products-list" class="selected-products-list"></ul>
                        <input type="hidden" name="selected_products" id="selected-products">
                    </div>
                    
                    <div class="input-field">
                        <p>Mô tả<span>*</span></p>
                        <textarea name="description" required maxlength="1000" placeholder="Nhập chi tiết sản phẩm" class="box"></textarea>
                    </div>
                    <div class="input-field">
                        <p>Banner mô tả<span>*</span></p>
                        <input type="file" name="background" accept="image/*" required class="box">
                    </div>
                    
                    <div class="flex-btn">
                        <input type="submit" name="publish" value="Lưu" class="btn" onclick="return confirm('Thêm sản phẩm thành công!');">
                    </div>
                </form>
            </div>
        </section> 
    </div>

    <script>
        function addProductName() {
            var select = document.getElementById("product-select");
            var selectedProductId = select.value; 
            var selectedProductName = select.options[select.selectedIndex].text; 
            var hiddenInput = document.getElementById("selected-products");
            
            // Lấy giá trị hiện tại và kiểm tra nếu nó không rỗng, thì chuyển đổi thành mảng
            var productIds = hiddenInput.value ? JSON.parse(hiddenInput.value) : [];

            // Kiểm tra xem sản phẩm đã được thêm vào danh sách chưa
            var alreadyExists = productIds.includes(selectedProductId);

            if (!alreadyExists) {
                var listItem = document.createElement("li");
                listItem.textContent = selectedProductName;

                var removeBtn = document.createElement("button");
                removeBtn.textContent = "x";
                removeBtn.classList.add("remove-btn");

                removeBtn.onclick = function() {
                    listItem.remove();
                    productIds = productIds.filter(id => id !== selectedProductId); // Xóa sản phẩm khỏi mảng
                    hiddenInput.value = JSON.stringify(productIds); // Cập nhật giá trị của input ẩn
                };

                listItem.appendChild(removeBtn);
                document.getElementById("selected-products-list").appendChild(listItem);

                productIds.push(selectedProductId); // Thêm ID sản phẩm vào mảng
                hiddenInput.value = JSON.stringify(productIds); // Lưu mảng ID vào input ẩn
            } else {
                alert("Sản phẩm này đã được thêm vào danh sách!");
            }
        }
    </script>   

    
    <script src="../js/admin_script.js"></script>
    <script src="../js/sweetalert.js"></script>
    <?php include '../component/alert.php'; ?>
</body>
</html>
