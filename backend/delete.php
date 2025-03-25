<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM products WHERE id = $id";
if ($conn->query($sql)) {
    header("Location: admin.php");
} else {
    echo "Lỗi: " . $conn->error;
}
?>