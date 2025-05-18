<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? ''; // Đổi từ matkhau sang password

    $stmt = $conn->prepare("SELECT id, username, matkhau, quyenhan FROM taikhoan WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

      // So sánh mật khẩu đã mã hóa MD5
      if (isset($user) && password_verify($password, $user['matkhau'])) {
        // Bắt đầu session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['quyenhan'] = $user['quyenhan'];
        
        header("Location: index.php");
        exit();
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
                    <h3 class="form-title"><i class="bi bi-plus-circle"></i> Đăng nhập</h3>
                    
                    <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="dangnhap.php">
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
                        
                        <div class="d-flex justify-content-between mt-2">
                            <a href="dangky.php" class="text-decoration-none" style="color: #E100FF;">
                                <i class="bi bi-person-plus"></i> Đăng ký tài khoản
                            </a>
                        </div>
                            
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="index.php" class="btn btnHuyBo">Hủy bỏ</a>
                            <button type="submit" name="login" class="btn btnLogin">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
    
    <!-- Bootstrap JS -->
</body>
</html>