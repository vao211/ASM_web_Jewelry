<?php


$conn = new mysqli("localhost:3306", "root", "", "ecomerce_web");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

else{
    // echo"Connect Success";
}


$admin_username = "admin";
$admin_password = password_hash("admin",  PASSWORD_DEFAULT);
$admin_email = "admin@gmail.com";

$sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')";
$cmd = $conn->prepare($sql);
$cmd->bind_param("sss", $admin_username, $admin_password, $admin_email);

if ($cmd->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $cmd->error;
}

$cmd->close();
$conn->close();
?>