<?php
require_once 'connect.php';


// Check if product ID exists
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Không tìm thấy sản phẩm cần sửa!";
    header("Location: danhsach.php");
    exit();
}

$id = intval($_GET['id']);

// Get product information to edit
$sql = "SELECT * FROM taikhoan WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Không tìm thấy sản phẩm!";
    header("Location: quanlytaikhoan.php");
    exit();
}

$account = $result->fetch_assoc();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from form
    $hoten = $conn->real_escape_string($_POST['hoten']);
    $sdt = $conn->real_escape_string($_POST['sdt']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $matkhau = $conn->real_escape_string($_POST['matkhau']);
    $quyenhan = $conn->real_escape_string($_POST['quyenhan']);
    
    // Update account information
    $sql_update = "UPDATE taikhoan SET 
                    hoten=?, 
                    sdt=?, 
                    email=?, 
                    username=?, 
                    matkhau=?, 
                    quyenhan=? 
                WHERE id=?";
    
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param(
        "ssssssi", 
        $hoten, 
        $sdt, 
        $email, 
        $username, 
        $matkhau, 
        $quyenhan, 
        $id
    );
    
    if ($stmt_update->execute()) {
        $_SESSION['success'] = "Cập nhật tài khoản thành công!";
        header("Location: quanlytaikhoan.php");
        exit();
    } else {
        $_SESSION['error'] = "Lỗi khi cập nhật tài khoản: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa Tài Khoản | PC Gaming</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Css/styles.css">
</head>

<body>
    <!-- Header & Navigation -->
    <?php include 'header.php'; ?>

    <!-- Main content -->
    <div class="container">
        <div class="edit-container">
            <h3 class="mb-4 header-title"><i class="bi bi-pencil-square"></i> Sửa Thông Tin Tài Khoản</h3>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="hoten" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="hoten" name="hoten" value="<?= htmlspecialchars($account['hoten']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="sdt" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="sdt" name="sdt" value="<?= htmlspecialchars($account['sdt']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($account['email']) ?>" required>
                </div>
                    
                <div class="mb-3">
                    <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($account['username']) ?>" required>
                </div>  

                <div class="mb-3 position-relative">
                    <label for="matkhau" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="matkhau" name="matkhau" value="<?= htmlspecialchars($account['matkhau']) ?>" required>
                        <button class="btnShow btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="quyenhan" class="form-label">Quyền hạn <span class="text-danger">*</span></label>
                    <select class="form-select" id="quyenhan" name="quyenhan" required>
                        <option value="Quản trị viên" <?= $account['quyenhan'] == 'Quản trị viên' ? 'selected' : '' ?>>Quản trị viên</option>
                        <option value="Nhân viên" <?= $account['quyenhan'] == 'Nhân viên' ? 'selected' : '' ?>>Nhân viên</option>
                        <option value="Khách hàng" <?= $account['quyenhan'] == 'Khách hàng' ? 'selected' : '' ?>>Khách hàng</option>
                    </select>
                </div>
                    <!-- Nút submit -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="quanlytaikhoan.php" class="btnQuayLai btn btn-secondary me-md-2">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btnLuu btn btn-primary">
                        <i class="bi bi-save"></i> Lưu thay đổi
                    </button>
                </div>
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