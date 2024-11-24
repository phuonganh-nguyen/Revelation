<?php 
//bao gồm một tệp PHP khác vào tệp hiện tại
    include 'component/connect.php';

    if (isset($_COOKIE['khach_id'])) {
        $user_id = $_COOKIE['khach_id'];
    } else {
        $user_id = '';
        header('location:login.php');
    }

    try {
        // Truy vấn lấy chất liệu từ cả hai cột
        $query = "
            SELECT DISTINCT chatlieu_1 AS chatlieu FROM motasanpham WHERE chatlieu_1 IS NOT NULL AND chatlieu_1 <> '0'
            UNION
            SELECT DISTINCT chatlieu_2 AS chatlieu FROM motasanpham WHERE chatlieu_2 IS NOT NULL AND chatlieu_2 <> '0'
        ";
        
        $stmt = $conn->prepare($query); // Chuẩn bị truy vấn
        $stmt->execute(); // Thực thi truy vấn
    
        // Lưu kết quả vào mảng
        $options = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
        $options = [];
    }

    try {
        // Truy vấn lấy màu sắc từ cả hai cột
        $colorQuery = "
            SELECT DISTINCT color_1 AS color FROM motasanpham WHERE color_1 IS NOT NULL AND color_1 <> '0'
            UNION
            SELECT DISTINCT color_2 AS color FROM motasanpham WHERE color_2 IS NOT NULL AND color_2 <> '0'
        ";
    
        $colorStmt = $conn->prepare($colorQuery); // Chuẩn bị truy vấn
        $colorStmt->execute(); // Thực thi truy vấn
    
        // Lưu kết quả vào mảng
        $colorOptions = $colorStmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
        $colorOptions = [];
    }


    try {
        // Truy vấn lấy tất cả các họa tiết
        $query = "SELECT DISTINCT hoatiet FROM motasanpham WHERE hoatiet IS NOT NULL AND hoatiet <> '0'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        // Lưu kết quả vào mảng
        $hoatiet_options = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Loại bỏ giá trị trùng lặp trong mảng (nếu có)
        $hoatiet_options = array_unique($hoatiet_options);

    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
        $hoatiet_options = [];
    }

    try {
        // Truy vấn lấy tất cả các phong cách từ 3 cột style_1, style_2, style_3
        $query = "
            SELECT DISTINCT style_1 AS style FROM motasanpham WHERE style_1 IS NOT NULL AND style_1 <> '0'
            UNION
            SELECT DISTINCT style_2 AS style FROM motasanpham WHERE style_2 IS NOT NULL AND style_2 <> '0'
            UNION
            SELECT DISTINCT style_3 AS style FROM motasanpham WHERE style_3 IS NOT NULL AND style_3 <> '0'
        ";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        // Lưu kết quả vào mảng
        $style_options = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
        // Loại bỏ giá trị trùng lặp trong mảng (nếu có)
        $style_options = array_unique($style_options);
    
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
        $style_options = [];
    }

    try {
        // Truy vấn lấy dữ liệu dịp sử dụng từ ba cột dip_1, dip_2, dip_3 và loại bỏ trùng lặp
        $query = "
            SELECT DISTINCT dip_1 AS dip FROM motasanpham WHERE dip_1 IS NOT NULL AND dip_1 <> '0'
            UNION
            SELECT DISTINCT dip_2 AS dip FROM motasanpham WHERE dip_2 IS NOT NULL AND dip_2 <> '0'
            UNION
            SELECT DISTINCT dip_3 AS dip FROM motasanpham WHERE dip_3 IS NOT NULL AND dip_3 <> '0'
        ";
    
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        // Lưu kết quả vào mảng
        $dip_options = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
        // Loại bỏ giá trị trùng lặp trong mảng (nếu có)
        $dip_options = array_unique($dip_options);
        
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
        $dip_options = [];
    }
    
    if (isset($_POST['submit'])) {
        // Lấy dữ liệu từ form và xử lý các trường hợp giá trị là "Tất cả" hoặc rỗng
        $chatlieu = isset($_POST['selected_products']) ? $_POST['selected_products'] : '';
        $color = isset($_POST['selected_colors']) ? $_POST['selected_colors'] : '';
        $style = isset($_POST['selected_styles']) ? $_POST['selected_styles'] : '';
        $dip = isset($_POST['selected_dips']) ? $_POST['selected_dips'] : '';
        $season = isset($_POST['selected_season']) ? $_POST['selected_season'] : '';
        $tuoi = isset($_POST['old']) ? intval($_POST['old']) : null;
    
        // Kiểm tra nếu giá trị là "Tất cả" hoặc không nhập thì gán giá trị 0
        $chatlieu = ($chatlieu === "Tất cả" || $chatlieu === '') ? 0 : $chatlieu;
        $color = ($color === "Tất cả" || $color === '') ? 0 : $color;
        $style = ($style === "Tất cả" || $style === '') ? 0 : $style;
        $dip = ($dip === "Tất cả" || $dip === '') ? 0 : $dip;
        $season = ($season === "Tất cả" || $season === '') ? 0 : $season;
        $tuoi = ($tuoi === null || $tuoi === '') ? 0 : $tuoi;
    
        try {
            // Kiểm tra nếu user_id đã tồn tại trong bảng user_preferences
            $queryCheck = "SELECT COUNT(*) FROM user_preferences WHERE user_id = :user_id";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->execute([':user_id' => $user_id]);
            $exists = $stmtCheck->fetchColumn();
    
            if ($exists) {
                // Nếu tồn tại, thực hiện UPDATE
                $queryUpdate = "UPDATE user_preferences
                                SET chatlieu = :chatlieu,
                                    color = :color,
                                    style = :style,
                                    dip = :dip,
                                    season = :season,
                                    tuoi = :tuoi
                                WHERE user_id = :user_id";
                $stmtUpdate = $conn->prepare($queryUpdate);
                $stmtUpdate->execute([
                    ':user_id' => $user_id,
                    ':chatlieu' => $chatlieu,
                    ':color' => $color,
                    ':style' => $style,
                    ':dip' => $dip,
                    ':season' => $season,
                    ':tuoi' => $tuoi,
                ]);
            } else {
                // Nếu không tồn tại, thực hiện INSERT
                $queryInsert = "INSERT INTO user_preferences (user_id, chatlieu, color, style, dip, season, tuoi)
                                VALUES (:user_id, :chatlieu, :color, :style, :dip, :season, :tuoi)";
                $stmtInsert = $conn->prepare($queryInsert);
                $stmtInsert->execute([
                    ':user_id' => $user_id,
                    ':chatlieu' => $chatlieu,
                    ':color' => $color,
                    ':style' => $style,
                    ':dip' => $dip,
                    ':season' => $season,
                    ':tuoi' => $tuoi,
                ]);
            }
    
            // Kiểm tra xem user_id đã tồn tại trong bảng recommended_products chưa
            $queryCheckRecommended = "SELECT COUNT(*) FROM recommended_products WHERE user_id = :user_id";
            $stmtCheckRecommended = $conn->prepare($queryCheckRecommended);
            $stmtCheckRecommended->execute([':user_id' => $user_id]);
            $existsRecommended = $stmtCheckRecommended->fetchColumn();
    
            if ($existsRecommended) {
                // Nếu đã tồn tại, xóa tất cả bản ghi cũ của user_id
                $queryDelete = "DELETE FROM recommended_products WHERE user_id = :user_id";
                $stmtDelete = $conn->prepare($queryDelete);
                $stmtDelete->execute([':user_id' => $user_id]);
            }   
            // Chuyển hướng sau khi xử lý thành công
            header('location: suggestion_processing/process_recommendation.php');
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }        
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>révélation - Search preferences page</title>
    <link rel="shortcut icon" href="images/logo1.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body id="login-page">
    <?php include 'component/user_header.php' ?>
    <img src="images/Banner/bg.jpg" alt="" id="bg-video">
    <div class="overlay"></div>
    <div class="form-container" style="margin-top: 10rem; margin-bottom: 4rem;">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Nhập thông tin gợi ý</h3>
                <div class="input-field">
                    <p>Chất liệu<span>*</span> (tối đa 2)</p>
                    <select name="product-select" id="product-select" onchange="addMaterial()" style="text-transform: capitalize">
                        <option value="Tất cả">Tất cả</option>
                        <?php foreach ($options as $chatlieu): ?>
                            <option value="<?php echo htmlspecialchars($chatlieu); ?>">
                                <?php echo htmlspecialchars($chatlieu); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <ul id="selected-products-list" class="selected-products-list"></ul>
                    <input type="hidden" name="selected_products" id="selected-products">
                </div>
                <div class="input-field">
                    <p>Màu sắc<span>*</span> (tối đa 2)</p>
                    <select name="color-select" id="color-select" onchange="addColor()" style="text-transform: capitalize">
                        <option value="Tất cả">Tất cả</option>
                        <?php foreach ($colorOptions as $color): ?>
                            <option value="<?php echo htmlspecialchars($color); ?>">
                                <?php echo htmlspecialchars($color); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <ul id="selected-colors-list" class="selected-products-list"></ul>
                    <input type="hidden" name="selected_colors" id="selected-colors">
                </div>
                <div class="input-field">
                    <p>Họa tiết<span>*</span></p>
                    <select name="hoatiet-select" id="hoatiet-select" style="text-transform: capitalize">
                        <option value="Tất cả">Tất cả</option>
                        <?php foreach ($hoatiet_options as $hoatiet): ?>
                            <option value="<?= htmlspecialchars($hoatiet); ?>">
                                <?= htmlspecialchars($hoatiet); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field">
                    <p>Phong cách yêu thích<span>*</span> (tối đa 3)</p>
                    <select name="style-select" id="style-select" onchange="addStyle()" style="text-transform: capitalize">
                        <option value="Tất cả">Tất cả</option>
                        <?php foreach ($style_options as $style): ?>
                            <option value="<?= htmlspecialchars($style); ?>">
                                <?= htmlspecialchars($style); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <ul id="selected-styles-list" class="selected-products-list"></ul>
                    <input type="hidden" name="selected_styles" id="selected-styles">
                </div>
                <div class="input-field">
                    <p>Dịp lý tưởng<span>*</span> (tối đa 3)</p>
                    <select name="dip-select[]" id="dip-select" onchange="addDip()" style="text-transform: capitalize">
                        <option value="Tất cả">Tất cả</option>
                        <?php foreach ($dip_options as $dip): ?>
                            <option value="<?= htmlspecialchars($dip); ?>">
                                <?= htmlspecialchars($dip); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <ul id="selected-dips-list" class="selected-products-list"></ul>
                    <input type="hidden" name="selected_dips" id="selected-dips">
                </div>
                <div class="input-field">
                    <p>Mùa lý tưởng<span>*</span></p>
                    <select name="" id="season-select" onchange="addSeason()" style="text-transform: capitalize">
                        <option value="Tất cả">Tất cả</option>
                        <option value="Xuân">Xuân</option>
                        <option value="Hè">Hè</option>
                        <option value="Thu">Thu</option>
                        <option value="Đông">Đông</option>
                    </select>
                    <ul id="selected-season-list" class="selected-products-list"></ul>
                    <input type="hidden" name="selected_season" id="selected-season">
                </div>

                <div class="input-field">
                    <p>Tuổi của bạn</p>
                    <input type="number" name="old" value="old" class="box" min="18">
                </div>
           <input type="submit" name="submit" value="Hiển thị" class="btn">
        </form>      
    </div>
    <script>
      // Hàm chung để quản lý danh sách các lựa chọn
function manageSelection(selectId, selectedItems, maxItems, listId, hiddenInputId) {
    const selectElement = document.getElementById(selectId);
    const selectedValue = selectElement.value;

    // Nếu không chọn gì hoặc giá trị đã tồn tại trong danh sách, thoát
    if (!selectedValue || selectedItems.includes(selectedValue)) return;

    // Nếu chọn "Tất cả", xử lý riêng
    if (selectedValue === "Tất cả") {
        if (selectedItems.includes("Tất cả")) return; // Nếu đã chọn "Tất cả" thì không làm gì
        selectedItems.length = 0; // Xóa tất cả lựa chọn
        selectedItems.push("Tất cả"); // Chỉ để "Tất cả"
    } else {
        // Kiểm tra giới hạn tối đa (sau khi chọn đủ 4 mùa)
        if (selectedItems.length >= maxItems) {
            alert(`Chỉ được chọn tối đa ${maxItems} mục.`);
            return;
        }
        // Thêm giá trị vào danh sách
        selectedItems.push(selectedValue);
    }

    // Nếu chọn đủ số lượng tối đa (4 mùa), tự động chuyển sang "Tất cả"
    if (selectedItems.length === 4) {
        // Xóa tất cả các mùa trước và chỉ chọn "Tất cả"
        selectedItems.length = 0;
        selectedItems.push("Tất cả");
        alert("Bạn đã chọn đủ 4 mùa, tự động chuyển sang 'Tất cả'.");
    }

    // Cập nhật danh sách hiển thị
    updateSelectedList(selectedItems, listId, hiddenInputId);

    // Vô hiệu hóa các lựa chọn đã chọn
    disableSelectedOptions(selectElement, selectedItems);
}

// Hàm xóa mục khỏi danh sách
function removeItem(index, selectedItems, listId, hiddenInputId) {
    selectedItems.splice(index, 1);
    updateSelectedList(selectedItems, listId, hiddenInputId);

    // Kích hoạt lại các lựa chọn đã xóa
    enableUnselectedOptions();
}

// Hàm cập nhật danh sách đã chọn
function updateSelectedList(selectedItems, listId, hiddenInputId) {
    const listElement = document.getElementById(listId);
    const hiddenInput = document.getElementById(hiddenInputId);

    // Xóa nội dung hiện tại
    listElement.innerHTML = "";

    // Thêm các mục vào danh sách
    selectedItems.forEach((item, index) => {
        const listItem = document.createElement("li");
        listItem.textContent = item;

        // Thêm nút xóa
        const removeButton = document.createElement("span");
        removeButton.textContent = "✕";
        removeButton.onclick = () => removeItem(index, selectedItems, listId, hiddenInputId);
        listItem.appendChild(removeButton);

        listElement.appendChild(listItem);
    });

    // Cập nhật input ẩn
    hiddenInput.value = selectedItems.join(",");
}

// Hàm vô hiệu hóa các lựa chọn đã chọn
function disableSelectedOptions(selectElement, selectedItems) {
    const options = selectElement.options;
    for (let i = 0; i < options.length; i++) {
        if (selectedItems.includes(options[i].value) || selectedItems.includes("Tất cả")) {
            options[i].disabled = true;
        }
    }
}

// Hàm kích hoạt lại các lựa chọn không được chọn
function enableUnselectedOptions() {
    const selectElement = document.getElementById("season-select");
    const options = selectElement.options;

    // Kích hoạt lại các mục không được chọn
    for (let i = 0; i < options.length; i++) {
        if (!selectedSeasons.includes(options[i].value)) {
            options[i].disabled = false;
        }
    }
}

// Các mảng lưu trữ các lựa chọn đã chọn
let selectedMaterials = [];
let selectedColors = [];
let selectedStyles = [];
let selectedDips = [];
let selectedSeasons = [];  // Thêm mảng cho mùa lý tưởng

// Các hàm thêm cho từng trường
function addMaterial() {
    manageSelection("product-select", selectedMaterials, 2, "selected-products-list", "selected-products");
}

function addColor() {
    manageSelection("color-select", selectedColors, 2, "selected-colors-list", "selected-colors");
}

function addStyle() {
    manageSelection("style-select", selectedStyles, 3, "selected-styles-list", "selected-styles");
}

function addDip() {
    manageSelection("dip-select", selectedDips, 3, "selected-dips-list", "selected-dips");
}

// Hàm thêm mùa lý tưởng (mới)
function addSeason() {
    manageSelection("season-select", selectedSeasons, 4, "selected-season-list", "selected-season");
}

// Các hàm xóa cho từng trường
function removeMaterial(index) {
    removeItem(index, selectedMaterials, "selected-products-list", "selected-products");
}

function removeColor(index) {
    removeItem(index, selectedColors, "selected-colors-list", "selected-colors");
}

function removeStyle(index) {
    removeItem(index, selectedStyles, "selected-styles-list", "selected-styles");
}

function removeDip(index) {
    removeItem(index, selectedDips, "selected-dips-list", "selected-dips");
}

function removeSeason(index) {  // Thêm hàm xóa cho mùa lý tưởng
    removeItem(index, selectedSeasons, "selected-season-list", "selected-season");
}

    </script>  
    <script src="js/sweetalert.js"></script>
    <script src="js/script.js"></script>
    <script src="js/user_script.js"></script>
    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'?>
</body>
</html>