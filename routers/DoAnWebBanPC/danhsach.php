<?php
require_once 'connect.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Xử lý xóa
if (isset($_GET['xoa'])) {
    // Verify CSRF token
    if (!isset($_GET['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        $_SESSION['error'] = "Token bảo mật không hợp lệ!";
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit();
    }

    $id = intval($_GET['xoa']);
    
    // Kiểm tra tồn tại trước khi xóa
    $check = $conn->prepare("SELECT id, hinhanh FROM sanpham WHERE id = ?");
    $check->bind_param("i", $id);
    
    if (!$check->execute()) {
        $_SESSION['error'] = "Lỗi truy vấn cơ sở dữ liệu";
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit();
    }
    
    $check->store_result();
    
    if ($check->num_rows > 0) {
        $check->bind_result($db_id, $db_hinhanh);
        $check->fetch();
        
        // Xóa ảnh sản phẩm nếu có
        if (!empty($db_hinhanh) && file_exists($db_hinhanh) && $db_hinhanh != 'images/no-image.jpg') {
            @unlink($db_hinhanh);
        }
        
        // Thực hiện xóa sản phẩm
        $sql = "DELETE FROM sanpham WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Xóa sản phẩm thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa sản phẩm: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Không tìm thấy sản phẩm!";
    }
    
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit();
}

// Lấy danh sách sách
$stmt = $conn->prepare("SELECT s.*, h.tenhsx FROM sanpham s LEFT JOIN hangsanxuat h ON s.idhsx = h.id ORDER BY s.id DESC");
$stmt->execute();
$result = $stmt->get_result();
$sanpham = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách Sản phẩm | Nhà sách online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="Css/styles.css">
  
</head>

<body>
    <!-- Header & Navigation -->
    <?php include 'header.php'; ?>
    <?php include '_navbar.php'; ?>

    <!-- Main content -->
    <div class="container">
        <div class="list-container">
            <h3 class="mb-4 header-title"><i class="bi bi-list"></i> Danh sách sản phẩm</h3>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Hình ảnh</th>
                            <th width="170px">HÃNG SẢN XUẤT</th>
                            <th width="400px">TÊN PC</th>
                            <th width="120px" colspan="2">GIÁ BÁN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($sanpham) > 0): ?>
                            <?php foreach ($sanpham as $index => $row): ?>
                                <?php 
                                $imgPath = (!empty($row['hinhanh']) && file_exists($row['hinhanh'])) ? 
                                    $row['hinhanh'] : 'images/no-image.jpg';
                                ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><img src="<?= htmlspecialchars($imgPath); ?>" alt="Ảnh sản phẩm" class="book-img" onerror="this.src='images/no-image.jpg';"></td>
                                    <td><?= htmlspecialchars($row['tenhsx'] ?? 'Không xác định'); ?></td>
                                    <td><?= htmlspecialchars($row['tenpc']); ?></td>
                                    <td><?= number_format($row['giaban'], 0, ',', '.'); ?> ₫</td>
                                    <td align="center">
                                        <a style="margin-right: 6px;" href="sua_sanpham.php?id=<?= $row['id'] ?>" class="btnSua btn btn-warning">Sửa</a>
                                        <a href="danhsach.php?xoa=<?= $row['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" 
                                           class="btnXoa btn btn-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có sản phẩm nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>