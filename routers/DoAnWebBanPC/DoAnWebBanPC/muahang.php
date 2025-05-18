<?php
require_once 'connect.php';

// Kiểm tra và xử lý tham số id sản phẩm
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];
    
    // Lấy thông tin sản phẩm
    $sql = "SELECT id, tenpc, giaban, hinhanh FROM sanpham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        // Lưu thông tin sản phẩm vào session (đã bỏ quantity)
        $_SESSION['direct_checkout'] = [
            $product_id => [
                'name' => $product['tenpc'],
                'price' => $product['giaban'],
                'image' => $product['hinhanh']
            ]
        ];
        
        // Tính toán các giá trị
        $cart = $_SESSION['direct_checkout'];
        $total = $product['giaban']; // Chỉ có 1 sản phẩm nên total = giá sản phẩm
        $shipping = 30000;
        $grand_total = $total + $shipping;
    } else {
        $_SESSION['error'] = "Sản phẩm không tồn tại";
        header("Location: sanpham.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Thiếu thông tin sản phẩm";
    header("Location: sanpham.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán | PC NP Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
   <link rel="stylesheet" href="Css/styles.css">
</head>
<body>
    <?php include 'header.php'; 
      include '_navbar.php';?>
    
    <div class="container py-5">
        <div class="row khinfo-container p-4">

            <!-- Cột trái: Thông tin khách hàng -->
            <div class="col-md-6">
                <h4 class="header-title mb-4"><i class="bi bi-person-lines-fill"></i> Thông tin khách hàng</h4>
                
                <form action="camon.php" method="post">
                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                    
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="note" class="form-label">Ghi chú (nếu có)</label>
                        <textarea class="form-control" id="note" name="note" rows="2"></textarea>
                    </div>
                    
                    <form action="camon.php" method="post">
                        <input type="hidden" name="product_id" value="<?= $product_id ?>">
                        <input type="hidden" name="product_name" value="<?= $product['tenpc'] ?>">
                        <input type="hidden" name="product_price" value="<?= $product['giaban'] ?>">
                        <input type="hidden" name="product_image" value="<?= $product['hinhanh'] ?>">
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-3">
                            <a href="sanpham.php" class="btnHuyBo btn btn-lg">
                                <i class="bi bi-x-circle"></i> Hủy bỏ
                            </a>
                            <button type="submit" class="btnComplete btn btn-lg">
                                <i class="bi bi-credit-card"></i> Hoàn tất đơn hàng
                            </button>
                        </div>
                        <script>
                            document.querySelector('form').addEventListener('submit', function(e) {
                                const requiredFields = ['fullname', 'email', 'phone', 'address'];
                                let isValid = true;
                                
                                requiredFields.forEach(field => {
                                    const input = document.getElementById(field);
                                    if (!input.value.trim()) {
                                        alert(`Vui lòng điền thông tin ${input.labels[0].textContent}`);
                                        input.focus();
                                        isValid = false;
                                        return false;
                                    }
                                });
                                
                                if (!isValid) {
                                    e.preventDefault();
                                }
                            });
                        </script>
                    </form>
                </form>
            </div>
            
            <!-- Cột phải: Thông tin sản phẩm -->
            <div class="col-md-6 divider ps-md-4">
                <h4 class="header-title mb-4"><i class="header-title bi bi-cart-check"></i> Đơn hàng của bạn</h4>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach($cart as $id => $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" 
                                         class="product-img-thumbnail me-3">
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                    </div>
                                </div>
                                <span class="text-primary"><?= number_format($item['price']) ?>₫</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td class="cart-label">Tạm tính:</td>
                                <td class="text-end"><?= number_format($total) ?>₫</td>
                            </tr>
                            <tr>
                                <td class="cart-label">Phí vận chuyển:</td>
                                <td class="text-end">30,000₫</td>
                            </tr>
                            <tr class="fw-bold">
                                <td class=>Tổng cộng:</td>
                                <td class="text-end text-danger"><?= number_format($grand_total) ?>₫</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3" style="font-weight: bold;">
                    <i class="bi bi-info-circle"></i> Vui lòng kiểm tra kỹ thông tin trước khi đặt hàng
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
</body>
</html>