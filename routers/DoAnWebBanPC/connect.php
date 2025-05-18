<?php
session_start();
/*$host = 'localhost:3307';
$username = 'root'; // Thay bằng username của bạn
$password = 'vertrigo'; // Thay bằng password của bạn
$password = ''; // Thay bằng password của bạn
$database = 'doanbanpc'; // Tên CSDL*/

// Nguyên
$host = 'localhost';
$username = 'root'; // Thay bằng username của bạn
$password = 'vertrigo'; // Thay bằng password của bạn
$database = 'doanbanpc'; // Tên CSDL

$conn = new mysqli($host, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// Thiết lập charset utf8
$conn->set_charset("utf8mb4");
?>