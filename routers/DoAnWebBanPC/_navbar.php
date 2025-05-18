<?php 
$quyenhan = 'Khách Hàng';
if(isset($_SESSION['quyenhan'])){
    $quyenhan = $_SESSION['quyenhan'];
}
$isLogin = false;
if(isset($_SESSION['id'])){
    $isLogin = true;
}
?>
<style>/* From Uiverse.io by JkHuger */ 
/* From Uiverse.io by JkHuger - Phiên bản thu nhỏ */
.wrapper {
  width: 80px; /* Giảm kích thước wrapper nếu cần */
}

.switch {
  position: relative;
  width: 80px; /* Thu nhỏ chiều rộng */
  height: 30px; /* Thu nhỏ chiều cao */
  margin: 0px;
  appearance: none;
  -webkit-appearance: none;
  background-color: rgb(4, 52, 73);
  background-size: cover;
  background-repeat: no-repeat;
  border-radius: 15px; /* Điều chỉnh border-radius cho phù hợp */
  transition: background-image .7s ease-in-out;
  outline: none;
  cursor: pointer;
  overflow: hidden;
}

.switch:checked {
  background-color: rgb(0, 195, 255);
  background-size: cover;
  transition: background-image 1s ease-in-out;
}

.switch:after {
  content: '';
  width: 28px; /* Thu nhỏ chiều rộng của nút tròn */
  height: 28px; /* Thu nhỏ chiều cao của nút tròn */
  border-radius: 50%;
  background-color: #fff;
  position: absolute;
  left: 1px; /* Điều chỉnh vị trí left */
  top: 1px; /* Điều chỉnh vị trí top */
  transform: translateX(0px);
  animation: off .7s forwards cubic-bezier(.8, .5, .2, 1.4);
  box-shadow: inset 3px -3px 2px rgba(53, 53, 53, 0.3); /* Điều chỉnh bóng đổ */
}

@keyframes off {
  0% {
    transform: translateX(50px); /* Điều chỉnh khoảng cách dịch chuyển */
    width: 28px;
  }

  50% {
    width: 45px; /* Điều chỉnh kích thước ở giữa */
    border-radius: 15px; /* Điều chỉnh border-radius ở giữa */
  }

  100% {
    transform: translateX(0px);
    width: 28px;
  }
}

.switch:checked:after {
  animation: on .7s forwards cubic-bezier(.8, .5, .2, 1.4);
  box-shadow: inset -3px -3px 2px rgba(53, 53, 53, 0.3); /* Điều chỉnh bóng đổ */
}

@keyframes on {
  0% {
    transform: translateX(0px);
    width: 28px;
  }

  50% {
    width: 45px; /* Điều chỉnh kích thước ở giữa */
    border-radius: 15px; /* Điều chỉnh border-radius ở giữa */
  }

  100% {
    transform: translateX(50px); /* Điều chỉnh khoảng cách dịch chuyển */
    width: 28px;
  }
}

.switch:checked:before {
  content: '';
  width: 9px; /* Thu nhỏ kích thước mặt trời */
  height: 9px; /* Thu nhỏ kích thước mặt trời */
  border-radius: 50%;
  position: absolute;
  left: 8px; /* Điều chỉnh vị trí left */
  top: 3px; /* Điều chỉnh vị trí top */
  transform-origin: 30px 6px; /* Điều chỉnh transform-origin */
  background-color: transparent;
  box-shadow: 3px -0.5px 0px #fff; /* Điều chỉnh bóng đổ */
  filter: blur(0px);
  animation: sun .7s forwards ease;
}

@keyframes sun {
  0% {
    transform: rotate(170deg);
    background-color: transparent;
    box-shadow: 3px -0.5px 0px #fff;
    filter: blur(0px);
  }

  50% {
    background-color: transparent;
    box-shadow: 3px -0.5px 0px #fff;
    filter: blur(0px);
  }

  90% {
    background-color: #f5daaa;
    box-shadow: 0px 0px 6px #f5deb4,
      0px 0px 12px #f5deb4,
      0px 0px 18px #f5deb4,
        inset 0px 0px 1px #efd3a3;
    filter: blur(0.5px);
  }

  100% {
    transform: rotate(0deg);
    background-color: #f5daaa;
    box-shadow: 0px 0px 6px #f5deb4,
      0px 0px 12px #f5deb4,
      0px 0px 18px #f5deb4,
        inset 0px 0px 1px #efd3a3;
    filter: blur(0.5px);
  }
}

.switch:before {
  content: '';
  width: 9px; /* Thu nhỏ kích thước mặt trăng */
  height: 9px; /* Thu nhỏ kích thước mặt trăng */
  border-radius: 50%;
  position: absolute;
  left: 8px; /* Điều chỉnh vị trí left */
  top: 3px; /* Điều chỉnh vị trí top */
  filter: blur(0.5px);
  background-color: #f5daaa;
  box-shadow: 0px 0px 6px #f5deb4,
  0px 0px 12px #f5deb4,
  0px 0px 18px #f5deb4,
    inset 0px 0px 1px #efd3a3;
  transform-origin: 30px 6px; /* Điều chỉnh transform-origin */
  animation: moon .7s forwards ease;
}

@keyframes moon {
  0% {
    transform: rotate(0deg);
    filter: blur(0.5px);
  }

  50% {
    filter: blur(0.5px);
  }

  90% {
    background-color: transparent;
    box-shadow: 3px -0.5px 0px #fff;
    filter: blur(0px);
  }

  100% {
    transform: rotate(170deg);
    background-color: transparent;
    box-shadow: 3px -0.5px 0px #fff;
    filter: blur(0px);
  }
}
</style>
<!-- CSS Links -->
<link id="dark-theme" href="Css/styles.css" rel="stylesheet">
<link id="light-theme" href="Css/light-theme.css" rel="stylesheet" disabled>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Main Navigation -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" 
                       href="index.php">
                       <i class="bi bi-house"></i> Trang chính
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'sanpham.php' ? 'active' : '' ?>" 
                       href="sanpham.php">
                       <i class="bi bi-grid"></i> Tất cả sản phẩm
                    </a>
                </li>
                
                <?php if(strtolower($quyenhan) === 'khách hàng'){ ?>
                    <!-- Guest Navigation -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dangky.php' ? 'active' : '' ?>" 
                           href="dangky.php">
                           <i class="bi bi-person-plus"></i> Đăng ký
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dangnhap.php' ? 'active' : '' ?>" 
                           href="dangnhap.php">
                           <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </a>
                    </li>
                <?php } else { ?>
                    <!-- Admin Navigation -->
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'hangsanxuat.php' ? 'active' : '' ?>" 
                           href="hangsanxuat.php">
                           <i class="bi bi-building-gear"></i> Nhà cung cấp
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'them_sanpham.php' ? 'active' : '' ?>" 
                           href="them_sanpham.php">
                           <i class="bi bi-plus-circle"></i> Thêm sản phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'danhsach.php' ? 'active' : '' ?>" 
                           href="danhsach.php">
                           <i class="bi bi-list-ul"></i> Danh sách SP
                        </a>
                    </li>
                    <?php if(strtolower($quyenhan) === 'quản trị viên'){ ?>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'quanlytaikhoan.php' ? 'active' : '' ?>" 
                               href="quanlytaikhoan.php">
                               <i class="bi bi-people"></i> Quản lý TK
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>

            <!-- Right Side Navigation -->
                <!-- Theme pro -->
                <div class="theme-switcher d-flex align-items-center gap-2">
                    <span class="theme-label">
                        <i class="bi bi-palette"></i>
                        Giao diện:
                    </span>
                    <div class="wrapper">
                        <input type="checkbox" id="checkbox" name="checkbox" class="switch" 
                            aria-label="Chuyển đổi giao diện sáng/tối">
                    </div>
                </div>
                <!-- User Info -->
                <div class="user-info d-flex align-items-center mx-4 gap-2">
                    <i class="bi bi-person-circle"></i>
                    <span>
                        <?php
                        if($isLogin === true){
                            echo "<strong>$quyenhan: </strong>".$_SESSION['username'];                                          
                        }else{
                      echo "<strong> Chưa đăng nhập </strong>";
                        }
                        ?>
                    </span>
                </div>

                <!-- Logout Button -->
                <?php if($isLogin === true){ ?>
                    <a class="nav-link text-danger" href="dangxuat.php">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<script src="JS/navBar.js"></script>