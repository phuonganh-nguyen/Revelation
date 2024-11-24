<?php
    include 'component/connect.php';

    $huyen_id = isset($_GET['huyen_id']) ? $_GET['huyen_id'] : null;

    // Kiểm tra xem giá trị huyen_id có hợp lệ không
    if (!$huyen_id) {
        die(json_encode(['error' => 'Không có giá trị huyện']));
    }

    // Lấy danh sách xã dựa trên huyện
    $stmt = $conn->prepare("SELECT xa_id, name FROM xa WHERE huyen_id = :huyen_id");
    $stmt->bindParam(':huyen_id', $huyen_id);
    $stmt->execute();

    $data = [];
    $data[] = [
        'id' => '',
        'name' => 'Chọn Xã/Phường/Thị trấn' // Tùy chọn mặc định
    ];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            'id' => $row['xa_id'],
            'name' => $row['name']
        ];
    }

    echo json_encode($data);
?>
