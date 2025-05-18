<?php
require_once 'connect.php';
// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['luu'])) {
    $tenpc = $_POST['tenpc'];
    $idhsx = $_POST['idhsx'];   
    $mainboard = $_POST['mainboard'];
    $cpu = $_POST['cpu'];
    $gpu = $_POST['gpu'];
    $ram = $_POST['ram'];
    $ssd = $_POST['ssd'];
    $giaban = $_POST['giaban'];
    $hinhanh = '';

    // Xử lý upload hình ảnh
    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES['hinhanh']['name']);
        move_uploaded_file($_FILES['hinhanh']['tmp_name'], $target_file);
        $hinhanh = $target_file;
    }

    // Thêm sản phẩm
    $sql = "INSERT INTO sanpham (tenpc, idhsx, mainboard, cpu, gpu, ram, ssd, giaban, hinhanh) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssssis", $tenpc, $idhsx, $mainboard, $cpu, $gpu, $ram, $ssd, $giaban, $hinhanh);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

// Lấy danh sách hãng sản xuất
$sql = "SELECT * FROM hangsanxuat ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm | PC NP Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="Css/styles.css">

</head>

<body>
    <!-- Header & Navigation -->
    <?php include 'header.php'; ?>
    <?php include '_navbar.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container">
                    <h3 class="form-title"><i class="bi bi-plus-circle"></i> Thêm Sản Phẩm Mới</h3>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên sản phẩm</label>
                                <input type="text" class="form-control" name="tenpc" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hãng sản xuất</label>
                                <select class="form-select" name="idhsx" required>
                                    <option value="">-- Chọn hãng --</option>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['tenhsx']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mainboard</label>
                                <input type="text" class="form-control" name="mainboard" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CPU</label>
                                <input type="text" class="form-control" name="cpu" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">GPU</label>
                                <input type="text" class="form-control" name="gpu" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">RAM</label>
                                <input type="text" class="form-control" name="ram" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SSD</label>
                                <input type="text" class="form-control" name="ssd" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá bán (₫)</label>
                                <input type="number" class="form-control" name="giaban" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh sản phẩm</label>
                            <input type="file" class="form-control" name="hinhanh" accept="image/*">
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="index.php" class="btn btnHuyBo">Hủy bỏ</a>
                            <button type="submit" name="luu" class="btn btnLuu">
                                <i class="bi bi-save"></i> Lưu sản phẩm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

  

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>