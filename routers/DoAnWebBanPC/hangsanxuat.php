<?php
require_once 'connect.php';

// Xử lý thêm/sửa
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['luu'])) {
    $tenhsx = $_POST['tenhsx'];
    
    // Sửa lại phần kiểm tra ID
    if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE hangsanxuat SET tenhsx = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("Lỗi prepare: " . $conn->error);
        }
        
        $stmt->bind_param("si", $tenhsx, $id);
        $action = "Cập nhật";
    } else {
        $sql = "INSERT INTO hangsanxuat (tenhsx) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $tenhsx);
        $action = "Thêm";
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "$action thành công!";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['error'] = "Lỗi: ".$conn->error;
    }
}

// Lấy dữ liệu để sửa
if (isset($_GET['sua'])) {
    $id = intval($_GET['sua']);
    echo "<script>console.log('ID cần sửa:', $id)</script>"; // Debug
    
    $sql = "SELECT * FROM hangsanxuat WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Lỗi prepare: " . $conn->error); // Kiểm tra lỗi SQL
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
    
    // Debug dữ liệu nhận được
    echo "<script>console.log('Dữ liệu sửa:', ", json_encode($edit_data), ")</script>";
    
    if (!$edit_data) {
        $_SESSION['error'] = "Không tìm thấy hãng sản xuất ID: $id";
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit();
    }
}

// Xử lý xóa
if (isset($_GET['xoa'])) {
    $id = intval($_GET['xoa']);
    
    // Kiểm tra tồn tại trước khi xóa
    $check = $conn->query("SELECT id FROM hangsanxuat WHERE id = $id");
    if ($check->num_rows > 0) {
        $sql = "DELETE FROM hangsanxuat WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Xóa thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Không tìm thấy hãng sản xuất!";
    }
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit();
}

// Lấy danh sách danh mục (đặt SAU phần xử lý POST)
$sql = "SELECT * FROM hangsanxuat ORDER BY id ASC";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hãng Sản Xuất | PC NP Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="Css/styles.css">
</head>

<body>
    <!-- Header & Navigation -->
    <?php include 'header.php'; ?>
    <?php include '_navbar.php'; ?>

     <div class="container mt-5">
        <div class="row">
            <!-- Form Section -->
            <div class="col-md-6">
                <div class="form-container">
                    <h3 class="text-aqua mb-4">
                        <i class="bi bi-plus-circle"></i> 
                        <?= isset($edit_data) ? 'Sửa Hãng Sản Xuất' : 'Thêm Hãng Sản Xuất' ?>
                    </h3>
                    <form method="POST">
                        <?php if (isset($edit_data)): ?>
                            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="form-label">Tên hãng sản xuất</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="tenhsx" 
                                   value="<?= isset($edit_data) ? htmlspecialchars($edit_data['tenhsx']) : '' ?>" 
                                   required 
                                   placeholder="Nhập tên hãng sản xuất">
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" name="luu" class="btn btn-primary">
                                <i class="bi bi-save"></i> 
                                <?= isset($edit_data) ? 'Cập nhật' : 'Thêm mới' ?>
                            </button>
                            <?php if (isset($edit_data)): ?>
                                <a href="hangsanxuat.php" class="btn btn-warning">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Section -->
            <div class="col-md-6">
                <div class="list-container">
                    <h3 class="text-aqua mb-4">
                        <i class="bi bi-building-gear"></i> Danh sách Hãng Sản Xuất
                    </h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="80px">ID</th>
                                    <th>TÊN HÃNG</th>
                                    <th width="100px">THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['tenhsx']) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?sua=<?= $row['id'] ?>" 
                                                       class="btn btn-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="?xoa=<?= $row['id'] ?>" 
                                                       class="btn btn-danger"
                                                       onclick="return confirm('Bạn có chắc muốn xóa?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            <i class="bi bi-inbox large-icon"></i>
                                            <p class="mt-2">Chưa có hãng sản xuất nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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