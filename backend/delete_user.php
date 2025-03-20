<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/index.html");
    exit();
}

//ko tu xoa acc cua minh
$id = (int)$_GET['id'];
if ($id == $_SESSION['user_id']) {
    echo "You cannot delete your account!";
    exit();
}

$sql = "DELETE FROM users WHERE id = $id";
if ($conn->query($sql)) {
    header("Location: admin_users.php");
} else {
    echo "Lỗi: " . $conn->error;
}
?>