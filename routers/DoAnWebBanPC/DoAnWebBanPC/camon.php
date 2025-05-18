<?php
// Bật báo lỗi chi tiết
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bắt đầu session và kết nối database
require_once 'connect.php';

// Kiểm tra nếu truy cập trực tiếp không qua form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Vui lòng đặt hàng qua trang thanh toán";
    header("Location: sanpham.php");
    exit();
}

// Danh sách các trường bắt buộc
$required_fields = [
    'product_id', 
    'product_name', 
    'product_price', 
    'product_image',
    'fullname', 
    'email', 
    'phone', 
    'address'
];

// Kiểm tra các trường bắt buộc
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "Thiếu thông tin bắt buộc: " . ucfirst(str_replace('_', ' ', $field));
        header("Location: checkout.php?id=".$_POST['product_id']);
        exit();
    }
}

// Xử lý và lọc dữ liệu
$product_id = (int)$_POST['product_id'];
$product_name = htmlspecialchars($_POST['product_name']);
$product_price = (float)$_POST['product_price'];
$product_image = htmlspecialchars($_POST['product_image']);
$fullname = htmlspecialchars($_POST['fullname']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars($_POST['phone']);
$address = htmlspecialchars($_POST['address']);
$note = isset($_POST['note']) ? htmlspecialchars($_POST['note']) : '';

// Tạo mã đơn hàng
$order_id = 'DH' . time() . rand(100, 999);

// Tính toán phí ship và tổng tiền
$shipping_fee = 30000;
$grand_total = $product_price + $shipping_fee;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công | PC NP Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="Css/styles.css">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php';
            include '_navbar.php';
     ?>

    <div class="container py-5">
        <div class="thank-you-card rounded-3 overflow-hidden mb-5">
            <!-- Header -->
            <div class="thank-you-header text-center">
                <i class="bi bi-check-circle-fill display-3 mb-3 check-icon"></i>
                <h1 class="mb-0">ĐẶT HÀNG THÀNH CÔNG</h1>
            </div>
            
            <!-- Body -->
            <div class="thank-you-body p-4 p-md-5">
                <!-- Mã đơn hàng -->
                <div class="order-id text-center mb-5">
                    <span class="order-id-badge">
                        <i class="bi bi-receipt me-2"></i>
                        MÃ ĐƠN HÀNG: <strong><?= $order_id ?></strong>
                    </span>
                </div>
                
                <div class="row g-4">
                    <!-- Thông tin khách hàng -->
                    <div class="col-md-6">
                        <div class="khinfo-card card h-100">
                            <div class="card-header" style="background-color: var(--neon-purple); color: #FFEE32; font-weight: bold;">
                                <i class="bi bi-person-lines-fill me-2"></i>
                                THÔNG TIN KHÁCH HÀNG
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <strong><i class="bi bi-person me-2"></i>HỌ TÊN:</strong>
                                        <div class="mt-1"><?= $fullname ?></div>
                                    </li>
                                    <li class="mb-3">
                                        <strong><i class="bi bi-envelope me-2"></i>EMAIL:</strong>
                                        <div class="mt-1"><?= $email ?></div>
                                    </li>
                                    <li class="mb-3">
                                        <strong><i class="bi bi-telephone me-2"></i>ĐIỆN THOẠI:</strong>
                                        <div class="mt-1"><?= $phone ?></div>
                                    </li>
                                    <li>
                                        <strong><i class="bi bi-geo-alt me-2"></i>ĐỊA CHỈ:</strong>
                                        <div class="mt-1"><?= $address ?></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sản phẩm đã đặt -->
                    <div class="col-md-6">
                        <div class="spinfo-card card h-100">
                            <div class="card-header " style="background-color: var(--neon-purple); color: #FFEE32; font-weight: bold;">
                                <i class="bi bi-box-seam me-2"></i>
                                SẢN PHẨM ĐÃ ĐẶT
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <img src="<?= $product_image ?>" class="product-img me-4" alt="<?= $product_name ?>">
                                    <div>
                                        <h5 class="mb-2"><?= $product_name ?></h5>
                                        <h4 class="text-danger mb-0"><?= number_format($product_price, 0, ',', '.') ?>₫</h4>
                                    </div>
                                </div>
                                
                                <div class="summary-box">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="summary-label">TẠM TÍNH:</span>
                                        <span class="summary-value"><?= number_format($product_price, 0, ',', '.') ?>₫</span>
                                    </div>
                                    <div class="summary-label d-flex justify-content-between mb-2">
                                        <span class="summary-label">PHÍ VẬN CHUYỂN:</span>
                                        <span class="summary-value">30.000₫</span>
                                    </div>
                                    <div class="summary-row total-row d-flex justify-content-between fw-bold fs-5">
                                        <span class="summary-label">TỔNG CỘNG:</span>
                                        <span class="total-value text-danger"><?= number_format($grand_total, 0, ',', '.') ?>₫</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Ghi chú của khách hàng -->
                    <?php if (!empty($note)): ?>
                        <div class="col-12 mt-4">
                            <div class="card h-100">
                                <div class="card-header" style="background-color: var(--neon-purple); color: #FFEE32; font-weight: bold;">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    GHI CHÚ CỦA BẠN
                                </div>
                                <div class="card-body">
                                    <?= $note ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Thông báo cảm ơn -->
                    <div class="col-12 mt-4">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2 fs-4" style="color: var(--neon-blue);"></i>
                                <div>
                                    <strong>CẢM ƠN BẠN ĐÃ ĐẶT HÀNG!</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Nút tiếp tục -->
                <div class="text-center mt-3 mb-4">
                    <a href="sanpham.php" class="btnTiepTuc btn btn-primary btn-lg px-5">
                        TIẾP TỤC MUA SẮM
                    </a>
                </div>
                    
            </div>
        </div>
    </div>

    <script>
        // Chống F5 resubmit
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>

<?php
// Xóa session giỏ hàng nếu có
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}
?>