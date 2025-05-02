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


require_once '/home/nctlhlqmhosting/db_config.php';

$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'nctlhlqmhosting_vaolu211';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'nctlhlqmhosting_vjewelry';
$port = getenv('DB_PORT') ?: 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {

}
$conn->set_charset("utf8mb4");
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Truy cập trực tiếp bị cấm');
}
?>