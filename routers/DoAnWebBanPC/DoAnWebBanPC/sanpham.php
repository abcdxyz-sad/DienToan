<?php
require_once 'connect.php';

// Authentication check
function checkAuth() {
    if (!isset($_SESSION['id'])) return ['logged_in' => false, 'role' => null];
    
    global $conn;
    $stmt = $conn->prepare("SELECT quyenhan FROM taikhoan WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['quyenhan'] = $user['quyenhan'];
        return ['logged_in' => true, 'role' => $user['quyenhan']];
    }
    
    session_destroy();
    return ['logged_in' => false, 'role' => null];
}

// Get filters
function getFilters() {
    return [
        'manufacturer' => isset($_GET['hangsanxuat']) ? (int)$_GET['hangsanxuat'] : 0,
        'price_min' => isset($_GET['price_min']) ? (int)$_GET['price_min'] : 0,
        'price_max' => isset($_GET['price_max']) ? (int)$_GET['price_max'] : 0,
        'sort' => $_GET['sort'] ?? 'newest'
    ];
}

// Build SQL query
function buildQuery($filters) {
    $sql = "SELECT sp.*, hsx.tenhsx AS hangsanxuat 
            FROM sanpham sp 
            JOIN hangsanxuat hsx ON sp.idhsx = hsx.id 
            WHERE 1=1";
    
    if ($filters['manufacturer'] > 0) {
        $sql .= " AND sp.idhsx = " . $filters['manufacturer'];
    }
    
    if ($filters['price_min'] > 0) {
        $sql .= " AND sp.giaban >= " . $filters['price_min'];
    }
    
    if ($filters['price_max'] > 0) {
        $sql .= " AND sp.giaban <= " . $filters['price_max'];
    }
    
    $sql .= match ($filters['sort']) {
        'price_asc' => " ORDER BY sp.giaban ASC",
        'price_desc' => " ORDER BY sp.giaban DESC", 
        'name_asc' => " ORDER BY sp.tenpc ASC",
        'name_desc' => " ORDER BY sp.tenpc DESC",
        default => " ORDER BY sp.id DESC"
    };
    
    return $sql;
}

// Main logic
$auth = checkAuth();
$filters = getFilters();

// Get manufacturers
$manufacturers = $conn->query("SELECT * FROM hangsanxuat ORDER BY tenhsx");

// Get filtered products
$products = $conn->query(buildQuery($filters));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC NP Online - Cửa hàng PC Gaming</title>
    
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="Css/styles.css">
</head>

<body>
    <?php 
    include 'header.php';
    include '_navbar.php';
    ?>

    <div class="container mt-5">
        <!-- Page Title -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="text-aqua"><i class="bi bi-stars"></i> Tất cả sản phẩm</h2>
                <hr class="border-neon">
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col">
                <div class="filter-container">
                    <form method="get" class="row g-3 align-items-center">
                        <!-- Manufacturer Filter -->
                        <div class="col-md-3">
                            <label class="filter-label">
                                <i class="bi bi-filter"></i> Hãng sản xuất:
                            </label>
                            <select name="hangsanxuat" class="form-select filter-select">
                                <option value="0">Tất cả hãng</option>
                                <?php while($manu = $manufacturers->fetch_assoc()): ?>
                                <option value="<?= $manu['id'] ?>" 
                                        <?= ($filters['manufacturer'] == $manu['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($manu['tenhsx']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="col-md-3">
                            <label class="filter-label">
                                <i class="bi bi-currency-dollar"></i> Khoảng giá:
                            </label>
                            <div class="input-group">
                                <input type="number" name="price_min" class="form-control filter-select" 
                                       placeholder="Từ" value="<?= $filters['price_min'] ?: '' ?>">
                                <span class="input-group-text">-</span>
                                <input type="number" name="price_max" class="form-control filter-select" 
                                       placeholder="Đến" value="<?= $filters['price_max'] ?: '' ?>">
                            </div>
                        </div>

                        <!-- Sort Options -->
                        <div class="col-md-3">
                            <label class="filter-label">
                                <i class="bi bi-sort-down"></i> Sắp xếp:
                            </label>
                            <select name="sort" class="form-select filter-select">
                                <option value="newest" <?= $filters['sort'] == 'newest' ? 'selected' : '' ?>>
                                    Mới nhất
                                </option>
                                <option value="price_asc" <?= $filters['sort'] == 'price_asc' ? 'selected' : '' ?>>
                                    Giá tăng dần
                                </option>
                                <option value="price_desc" <?= $filters['sort'] == 'price_desc' ? 'selected' : '' ?>>
                                    Giá giảm dần
                                </option>
                                <option value="name_asc" <?= $filters['sort'] == 'name_asc' ? 'selected' : '' ?>>
                                    Tên A-Z
                                </option>
                                <option value="name_desc" <?= $filters['sort'] == 'name_desc' ? 'selected' : '' ?>>
                                    Tên Z-A
                                </option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn filter-btn me-2">
                                <i class="bi bi-funnel"></i> Lọc
                            </button>
                            <a href="?" class="btn unfilter-btn btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Xóa lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4">
            <?php if ($products->num_rows > 0): ?>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 product-card">
                            <div class="product-img-container">
                                <img src="<?= htmlspecialchars($product['hinhanh'] ?? 'images/placeholder.jpg') ?>" 
                                     alt="<?= htmlspecialchars($product['tenpc']) ?>" 
                                     class="product-img">
                            </div>
                            
                            <div class="card-body">
                                <h5 class="product-title">
                                    <?= htmlspecialchars($product['tenpc']) ?>
                                </h5>
                                
                                <div class="product-specs">
                                    <p><i class="bi bi-tag"></i> <strong>Hãng:</strong> 
                                        <?= htmlspecialchars($product['hangsanxuat']) ?>
                                    </p>
                                    <p><i class="bi bi-motherboard"></i> <strong>Mainboard:</strong>
                                        <?= htmlspecialchars($product['mainboard']) ?>
                                    </p>
                                    <p><i class="bi bi-cpu"></i> <strong>CPU:</strong>
                                        <?= htmlspecialchars($product['cpu']) ?>
                                    </p>
                                    <p><i class="bi bi-gpu-card"></i> <strong>GPU:</strong>
                                        <?= htmlspecialchars($product['gpu']) ?>
                                    </p>
                                </div>
                                
                                <div class="product-price">
                                    <?= number_format($product['giaban']) ?>₫
                                </div>
                            </div>

                            <div class="card-footer bg-transparent">
                                <?php if(strtolower($auth['role']) === 'khách hàng'): ?>
                                    <a href="muahang.php?id=<?= $product['id'] ?>" class="btn btnMuaHang">
                                        <i class="bi bi-cart-plus"></i> Mua hàng
                                    </a>
                                <?php else: ?>
                                    <a href="sua_sanpham.php?id=<?= $product['id'] ?>" class="btn btnMuaHang">
                                        <i class="bi bi-info-circle"></i> Chi tiết
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <h4>Không tìm thấy sản phẩm nào</h4>
                        <p>Vui lòng thử lại với bộ lọc khác.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
   
</body>
</html>

<?php $conn->close(); ?>