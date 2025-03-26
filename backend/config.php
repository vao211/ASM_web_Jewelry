<?php
// $conn = new mysqli("localhost:3306", "root", "", "jewelry_db");
// if ($conn->connect_error) {
//     die("Kết nối thất bại: " . $conn->connect_error);
// }

// else{
//     // echo"Connect Success";
// }


$servername = "Vubuntu.vinh.com";
$username = "vao211"; 
$password = "Vinh2112005";
$dbname = "jewelry_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
} else {
    // echo "Kết nối thành công!";
}
?>
