<?php
require_once 'connect.php'; // Database connection

// Kiểm tra trạng thái đăng nhập và quyền hạn từ database
$isLoggedIn = isset($_SESSION['id']);
$quyenhan = 'Khách Hàng';

if ($isLoggedIn) {
    $stmt = $conn->prepare("SELECT quyenhan FROM taikhoan WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $quyenhan = $user['quyenhan'];
        $_SESSION['quyenhan'] = $quyenhan; // Cập nhật session
    } else {
        // Tài khoản không tồn tại trong database
        session_unset();
        session_destroy();
        $isLoggedIn = false;
    }
    $stmt->close();
}

// Get latest products from database
$sql = "SELECT sp.*, hsx.tenhsx AS hangsanxuat 
        FROM sanpham sp 
        JOIN hangsanxuat hsx ON sp.idhsx = hsx.id 
        ORDER BY sp.id DESC 
        LIMIT 8";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC NP Online - Cửa hàng PC Gaming</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="Css/styles.css">
</head>
<!-- <body class="bg-light"> -->
    <body>        
    <!-- Header -->
<?php
    include 'header.php'; 
    include '_navbar.php';
    ?>

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col">
                <h2 class="text-aqua"><i class="bi bi-stars"></i> Sản phẩm mới nhất</h2>
                <hr class="border-neon">
            </div>
        </div>

        <div class="row g-4">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 product-card">
                            <div class="product-img-container">
                                <img src="<?= htmlspecialchars($row['hinhanh'] ?? 'images/placeholder.jpg') ?>" 
                                     alt="<?= htmlspecialchars($row['tenpc']) ?>" 
                                     class="product-img">
                            </div>
                            
                            <div class="card-body">
                                <h5 class="product-title"><?= htmlspecialchars($row['tenpc']) ?></h5>
                                
                                <div class="product-specs">
                                    <p><i class="bi bi-tag"></i> <strong>Hãng:</strong> <?= htmlspecialchars($row['hangsanxuat']) ?></p>
                                    <p><i class="bi bi-motherboard"></i> <strong>Mainboard:</strong> <?= htmlspecialchars($row['mainboard']) ?></p>
                                    <p><i class="bi bi-cpu"></i> <strong>CPU:</strong> <?= htmlspecialchars($row['cpu']) ?></p>
                                    <p><i class="bi bi-gpu-card"></i> <strong>GPU:</strong> <?= htmlspecialchars($row['gpu']) ?></p>
                                </div>
                                
                                <div class="product-price">
                                    <?= number_format($row['giaban']) ?>₫
                                </div>
                            </div>
                            <?php if($isLoggedIn === true && strtolower($quyenhan) === 'khách hàng') { ?>
                            <div class="card-footer bg-transparent">
                                <a href="muahang.php?id=<?= $row['id'] ?>" class="btn btnMuaHang">
                                    <i class="bi bi-cart-plus"></i> Mua hàng
                                </a>
                                <hr>
                                <a href="sua_sanpham.php?id=<?= $row['id'] ?>" class="btn btnMuaHang">
                                    <i class="bi "></i> Chi tiết
                                </a>
                            </div>
                            <?php } else { ?>
                                <div class="card-footer bg-transparent">
                                <a href="sua_sanpham.php?id=<?= $row['id'] ?>" class="btn btnMuaHang">
                                    <i class="bi "></i> Chi tiết
                                </a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Hiện chưa có sản phẩm nào
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="sanpham.php" class="btn btnSanPhamAll">
                <i class="bi bi-grid"></i> Xem tất cả sản phẩm
            </a>
        </div>
    </div>

   
    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
  
</body>
</html>

<?php
$conn->close();
?>