<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dangky'])) {
    $hoten    = trim($_POST['fullname'] ?? '');
    $sdt      = trim($_POST['phone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $matkhau  = $_POST['password'] ?? '';
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($hoten) || empty($sdt) || empty($email) || empty($username) || empty($matkhau)) {
        die("Vui lòng điền đầy đủ thông tin.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email không hợp lệ.");
    }
    
    if (strlen($sdt) !== 10 || !ctype_digit($sdt)) {
        die("Số điện thoại phải có 10 chữ số.");
    }

    // Mã hóa mật khẩu an toàn hơn với password_hash()
    $matkhau_hashed = password_hash($matkhau, PASSWORD_DEFAULT);
    
    $quyenhan = 'Khách hàng'; // Giá trị mặc định

    try {
        $sql = "INSERT INTO taikhoan (hoten, sdt, email, username, matkhau, quyenhan) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $hoten, $sdt, $email, $username, $matkhau_hashed, $quyenhan);
        
        if ($stmt->execute()) {
            $_SESSION['register_success'] = true;
            header("Location: dangnhap.php");
            exit();
        } else {
            throw new Exception("Lỗi khi thêm tài khoản.");
        }
    } catch (Exception $e) {
        die("Lỗi hệ thống: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    

    <link rel="stylesheet" href="Css/styles.css">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php';
      include '_navbar.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container">
                    <h3 class="form-title"><i class="bi bi-plus-circle"></i> Đăng ký tài khoản</h3>
                    
                    <form method="POST" action="dangky.php" enctype="multipart/form-data">
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
                        <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>  

                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btnShow btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>                                              
                        
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="index.php" class="btn btnHuyBo">Hủy bỏ</a>
                        <button type="submit" name="dangky" class="btn btnDangKy">
                        <i class="bi bi-save"></i> Đăng ký tài khoản
                        </button>
                    </div>
                </div>
                </form>

                    <!-- Thêm Font Awesome (nếu chưa có) -->
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

                    <script>
                        document.getElementById('togglePassword').addEventListener('click', function() {
                            const passwordInput = document.getElementById('password');
                            const icon = this.querySelector('i');
                            
                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                icon.classList.replace('fa-eye', 'fa-eye-slash');
                            } else {
                                passwordInput.type = 'password';
                                icon.classList.replace('fa-eye-slash', 'fa-eye');
                            }
                        });
                    </script>    
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>