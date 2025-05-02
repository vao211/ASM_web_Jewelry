<?php
// $conn = new mysqli("localhost:3306", "root", "", "jewelry_db");
// if ($conn->connect_error) {
//     die("Kết nối thất bại: " . $conn->connect_error);
// }

// else{
//     // echo"Connect Success";
// }

// if (getenv("JAWSDB_URL")) {
//     $dbUrl = parse_url(getenv("JAWSDB_URL"));
//     $servername = $dbUrl["host"];
//     $username = $dbUrl["user"];
//     $password = $dbUrl["pass"];
//     $dbname = ltrim($dbUrl["path"], "/");
//     $port = $dbUrl["port"] ?: 3306;
// } else {
//     $servername = "localhost:3306";
//     $username = "root";
//     $password = "";
//     $dbname = "jewelry_db";
//     $port = 3306;
// }

// $conn = new mysqli($servername, $username, $password, $dbname, $port);

// if ($conn->connect_error) {
//     die("Kết nối thất bại: " . $conn->connect_error);
// } else {
//     // echo "Kết nối thành công!";
// }

// Thông tin kết nối cơ sở dữ liệu cho iNET hosting
$servername = "localhost";
$username = "nctlhlqmhosting_vaolu211";
$password = "Vinh2112005."; // Thay bằng mật khẩu của user nctlhlqmhosting_vaolu211
$dbname = "jewelry_db"; // Xác minh tên cơ sở dữ liệu trong cPanel
$port = 3306; // Mặc định cho MySQL trên iNET

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
} else {
    // echo "Kết nối thành công!";
}

// Đặt encoding UTF-8 để hỗ trợ tiếng Việt (nếu cần)
$conn->set_charset("utf8mb4");
?>