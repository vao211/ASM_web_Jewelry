<?php
// $conn = new mysqli("localhost:3306", "root", "", "jewelry_db");
// if ($conn->connect_error) {
//     die("Kết nối thất bại: " . $conn->connect_error);
// }

// else{
//     // echo"Connect Success";
// }


// $servername = "Vubuntu.vinh.com";
// $username = "vao211"; 
// $password = "Vinh2112005";
// $dbname = "jewelry_db";

// $conn = new mysqli($servername, $username, $password, $dbname);

// if ($conn->connect_error) {
//     die("Kết nối thất bại: " . $conn->connect_error);
// } else {
//     // echo "Kết nối thành công!";
// }

if (getenv("JAWSDB_URL")) {
    $dbUrl = parse_url(getenv("JAWSDB_URL"));
    $servername = $dbUrl["host"];
    $username = $dbUrl["user"];
    $password = $dbUrl["pass"];
    $dbname = ltrim($dbUrl["path"], "/");
    $port = $dbUrl["port"] ?: 3306;
} else {
    $servername = "Vubuntu.vinh.com";
    $username = "vao211";
    $password = "Vinh2112005";
    $dbname = "jewelry_db";
    $port = 3306;
}

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
} else {
    // echo "Kết nối thành công!";
}

?>
