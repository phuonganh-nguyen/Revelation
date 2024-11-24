<?php
// Kết nối cơ sở dữ liệu
include '../component/connect.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_COOKIE['khach_id'])) {
    header('location:login.php');
    exit();
}
$user_id = $_COOKIE['khach_id'];

// Hàm thực hiện yêu cầu POST
function execPostRequest($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// Kiểm tra nếu có bill_id trong URL
if (isset($_GET['bill_id'])) {
    $bill_id = $_GET['bill_id'];

    // Truy vấn thông tin hóa đơn từ bảng bill
    $select_bill = $conn->prepare("SELECT tongthanhtoan FROM `bill` WHERE bill_id = ?");
    $select_bill->execute([$bill_id]);

    // Kiểm tra xem hóa đơn có tồn tại không
    if ($select_bill->rowCount() > 0) {
        $bill_info = $select_bill->fetch(PDO::FETCH_ASSOC);
        $amount = $bill_info['tongthanhtoan']; // Số tiền thanh toán

        // Thông tin thanh toán
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua MoMo ATM";
        $orderId = $bill_id;
        $redirectUrl = "http://localhost/web/thanhtoan/email_momo.php?bill_id=" . $bill_id;
        $ipnUrl = "http://localhost/web/thanhtoan/email_momo.php";
        $requestId = time() . "";
        $requestType = "payWithATM";
        $extraData = ""; // Thông tin bổ sung

        // Tạo chuỗi hash để ký
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        // Dữ liệu gửi đi
        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount, // Gán giá trị amount
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        // Gửi yêu cầu thanh toán đến MoMo
        $result = execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // Giải mã JSON

        // Chuyển hướng đến trang thanh toán của MoMo
        header('Location: ' . $jsonResult['payUrl']);
        exit();
    } else {
        echo "<p>Không tìm thấy hóa đơn.</p>";
        exit();
    }
}

// Xử lý phản hồi từ MoMo
$data = file_get_contents('php://input');
$json_data = json_decode($data, true);

if (isset($json_data['status'], $json_data['orderId'])) {
    $status = $json_data['status'];
    $orderId = $json_data['orderId'];

    // Truy vấn thông tin hóa đơn từ bảng bill dựa trên orderId
    $select_bill = $conn->prepare("SELECT * FROM `bill` WHERE order_id = ?");
    $select_bill->execute([$orderId]);

    if ($select_bill->rowCount() > 0) {
        $bill_info = $select_bill->fetch(PDO::FETCH_ASSOC);
        $bill_id = $bill_info['bill_id'];
        $user_id = $bill_info['user_id'];

        if ($status == 'success') {
            header('Location: email_momo.php'); // Chuyển hướng về trang order.php
            exit();
        } else {
            header('Location: ../web/thongtinthanhtoan.php'); // Chuyển hướng về trang order.php
            exit();
        }
    } else {
        echo "Không tìm thấy hóa đơn tương ứng.";
    }
} else {
    echo "Dữ liệu không hợp lệ.";
}
?> 
