<?php

include 'backend/config.php';

$sql = "INSERT INTO categories (name, description) VALUES 
    ('Men''s Fashion', 'Thời trang nam'),
    ('Women''s Fashion', 'Thời trang nữ'),
    ('Accessories', 'Phụ kiện')";

if ($conn->query($sql) === TRUE) {
    echo "ok";
} else {
    echo "Lỗi: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>