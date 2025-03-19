<?php
$conn = new mysqli("localhost:3306", "root", "", "jewelry_db");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

else{
    echo"Connect Success";
}
?>