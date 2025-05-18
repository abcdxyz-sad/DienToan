<?php
require_once 'connect.php';

// Kiểm tra nếu không có ID sản phẩm
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Không tìm thấy sản phẩm cần sửa!";
    header("Location: danhsach.php");
    exit();
}

$id = intval($_GET['id']);

// Lấy thông tin sản phẩm cần sửa
$sql = "SELECT * FROM sanpham WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Không tìm thấy sản phẩm!";
    header("Location: danhsach.php");
    exit();
}

$product = $result->fetch_assoc();

// Lấy danh sách hãng sản xuất
$sql_hsx = "SELECT * FROM hangsanxuat ORDER BY tenhsx ASC";
$result_hsx = $conn->query($sql_hsx);

// Xử lý form khi submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $tenpc = $conn->real_escape_string($_POST['tenpc']);
    $giaban = intval($_POST['giaban']);
    $idhsx = intval($_POST['idhsx']);
    $ram = $conn->real_escape_string($_POST['ram']);
    $ssd = $conn->real_escape_string($_POST['ssd']);
    $mainboard = $conn->real_escape_string($_POST['mainboard']);
    $gpu = $conn->real_escape_string($_POST['gpu']);
    $cpu = $conn->real_escape_string($_POST['cpu']);
    
    // Xử lý upload ảnh mới
    $hinhanh = $product['hinhanh'];
    
    if (!empty($_FILES['hinhanh']['name'])) {
        $target_dir = "images/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        // Kiểm tra và upload file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_ext), $allowed_types)) {
            if (move_uploaded_file($_FILES['hinhanh']['tmp_name'], $target_file)) {
                // Xóa ảnh cũ nếu không phải ảnh mặc định
                if ($hinhanh != 'images/no-image.jpg' && file_exists($hinhanh)) {
                    unlink($hinhanh);
                }
                $hinhanh = $target_file;
            } else {
                $_SESSION['error'] = "Lỗi khi upload ảnh!";
            }
        } else {
            $_SESSION['error'] = "Chỉ chấp nhận file ảnh JPG, JPEG, PNG & GIF.";
        }
    }
    
    // Cập nhật thông tin sản phẩm
    $sql_update = "UPDATE sanpham SET 
                    tenpc=?, 
                    giaban=?, 
                    idhsx=?, 
                    hinhanh=?, 
                    ram=?, 
                    ssd=?, 
                    mainboard=?, 
                    gpu=?, 
                    cpu=?
                WHERE id=?";
    
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param(
        "siissssssi", 
        $tenpc, 
        $giaban, 
        $idhsx, 
        $hinhanh, 
        $ram, 
        $ssd, 
        $mainboard, 
        $gpu, 
        $cpu, 
        $id
    );
    
    if ($stmt_update->execute()) {
        $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
        header("Location: danhsach.php");
        exit();
    } else {
        $_SESSION['error'] = "Lỗi khi cập nhật sản phẩm: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm | PC Gaming</title>
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
        <div class="edit-container">
            <h3 class="mb-4 header-title"><i class="bi bi-pencil-square"></i> Sửa Thông Tin PC</h3>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="row equal-height">
                    <!-- Thông tin cơ bản -->
                    <div class="col-md-6">
                        <div class="specs-container h-100">
                            <h5 class="specs-title"><i class="bi bi-info-circle"></i> Thông tin cơ bản</h5>
                            
                            <!-- Tên sản phẩm -->
                            <div class="mb-3">
                                <label for="tenpc" class="form-label">Tên PC</label>
                                <input type="text" class="form-control" id="tenpc" name="tenpc" 
                                    value="<?= htmlspecialchars($product['tenpc']) ?>" required>
                            </div>

                            <!-- Giá bán -->
                            <div class="mb-3">
                                <label for="giaban" class="form-label">Giá bán (₫)</label>
                                <input type="number" class="form-control" id="giaban" name="giaban" 
                                    value="<?= htmlspecialchars($product['giaban']) ?>" required min="0">
                            </div>

                            <!-- Hãng sản xuất -->
                            <div class="mb-3">
                                <label for="idhsx" class="form-label">Hãng sản xuất</label>
                                <select class="form-select" id="idhsx" name="idhsx" required>
                                    <option value="">-- Chọn hãng sản xuất --</option>
                                    <?php while ($hsx = $result_hsx->fetch_assoc()): ?>
                                        <option value="<?= $hsx['id'] ?>" 
                                            <?= ($hsx['id'] == $product['idhsx']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($hsx['tenhsx']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <!-- Ảnh sản phẩm -->
                            <div class="upload-container mt-auto">
                                <label for="hinhanh" class="form-label">Ảnh sản phẩm mới</label>
                                <input class="form-control" type="file" id="hinhanh" name="hinhanh">
                            </div>
                        </div>
                    </div>

                    <!-- Thông số kỹ thuật -->
                    <div class="col-md-6">
                        <div class="specs-container h-100">
                            <h5 class="specs-title"><i class="bi bi-motherboard"></i> Thông số kỹ thuật</h5>
                            
                            <div class="row">
                                <!-- Mainboard -->
                                <div class="col-md-12 mb-3">
                                    <label for="mainboard" class="form-label">Mainboard</label>
                                    <input type="text" class="form-control" id="mainboard" name="mainboard" 
                                        value="<?= htmlspecialchars($product['mainboard']) ?>">
                                </div>

                                <!-- CPU -->
                                <div class="col-md-12 mb-3">
                                    <label for="cpu" class="form-label">CPU</label>
                                    <input type="text" class="form-control" id="cpu" name="cpu" 
                                        value="<?= htmlspecialchars($product['cpu']) ?>">
                                </div>
                                
                                <!-- GPU -->
                                <div class="col-md-12 mb-3">
                                    <label for="gpu" class="form-label">GPU (VGA)</label>
                                    <input type="text" class="form-control" id="gpu" name="gpu" 
                                        value="<?= htmlspecialchars($product['gpu']) ?>">
                                </div>
                                
                                <!-- RAM -->
                                <div class="col-md-6 mb-3">
                                    <label for="ram" class="form-label">RAM</label>
                                    <input type="text" class="form-control" id="ram" name="ram" 
                                        value="<?= htmlspecialchars($product['ram']) ?>">
                                </div>
                                
                                <!-- SSD -->
                                <div class="col-md-6 mb-3">
                                    <label for="ssd" class="form-label">Ổ cứng SSD</label>
                                    <input type="text" class="form-control" id="ssd" name="ssd" 
                                        value="<?= htmlspecialchars($product['ssd']) ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Nút submit -->                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="index.php" class="btnQuayLai btn btn-secondary me-md-2">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                     <?php if(isset($_SESSION['username']) && strtolower($_SESSION['quyenhan']) !== 'khách hàng') {?>
                    <button type="submit" class="btnLuu btn btn-primary">
                        <i class="bi bi-save"></i> Lưu thay đổi
                    </button>
                </div>
                <?php } ?>
            </form>
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