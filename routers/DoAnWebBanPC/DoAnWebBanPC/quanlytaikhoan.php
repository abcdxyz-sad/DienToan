<?php
require_once 'connect.php';

// Xử lý xóa
if (isset($_GET['xoa'])) {
    $id = intval($_GET['xoa']);
    
    // Kiểm tra tồn tại trước khi xóa
    $check = $conn->prepare("SELECT id FROM taikhoan WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        // Xóa ảnh sản phẩm nếu có
        
        
        // Thực hiện xóa sản phẩm
        $sql = "DELETE FROM taikhoan WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Xóa tk thành công!";
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
$sql = "SELECT * FROM taikhoan";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách Sách | Nhà sách online</title>
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

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                        <th width="">Họ & Tên</th>
                        <th width="">Số điện thoại</th>
                        <th width="">Email</th>
                        <th width="">Tên đăng nhập</th>
                        <th width="">Mật khẩu</th>
                        <th width="">Chức vụ</th>
<th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['hoten']); ?></td>
                                    <td><?= htmlspecialchars($row['sdt']); ?></td>
                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                    <td><?= htmlspecialchars($row['username']); ?></td>
                                    <td><?= htmlspecialchars($row['matkhau']); ?></td>
                                    <td><?= htmlspecialchars($row['quyenhan']); ?></td>
                                    <td><a href="sua_taikhoan.php?id=<?= $row['id'] ?>" class="btnSua btn btn-warning">Sửa</a><br>
                                    <a href="quanlytaikhoan.php?xoa=<?= $row['id'] ?>" class="btnXoa btn btn-danger">Xóa</a></td>
                                </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-warning py-4">Không có sản phẩm nào</td>
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
</body>

</html>

<?php
$conn->close();
?>
