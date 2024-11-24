<?php
    include 'component/connect.php';

    $tinh_id = isset($_GET['tinh_id']) ? $_GET['tinh_id'] : null;

    // Kiểm tra xem giá trị tinh_id có hợp lệ không
    if (!$tinh_id) {
        die(json_encode(['error' => 'Không có giá trị tỉnh']));
    }

    // Lấy danh sách huyện dựa trên tỉnh
    $stmt = $conn->prepare("SELECT huyen_id, name FROM huyen WHERE tinh_id = :tinh_id");
    $stmt->bindParam(':tinh_id', $tinh_id);
    $stmt->execute();

    $data = [];
    $data[] = [
        'id' => '',
        'name' => 'Chọn Quận/Huyện' // Tùy chọn mặc định
    ];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            'id' => $row['huyen_id'],
            'name' => $row['name']
        ];
    }

    echo json_encode($data);
?>
