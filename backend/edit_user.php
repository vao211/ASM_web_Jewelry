<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/index.html");
    exit();
}

//_POST trong form
if (!isset($_GET['id']) && !isset($_POST['edit_id'])) {
    header("Location: admin_users.php");
    exit();
}

if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];
} 
elseif (isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
}
else {
    $edit_id = null;
}

//check exist and valid id
if ($edit_id <= 0) {
    echo "Invalid user ID!";
    exit();
}
$edit_result = $conn->query("SELECT * FROM users WHERE id=$edit_id");
if (!$edit_result || $edit_result->num_rows == 0) {
    echo "User not existed !";
    exit();
}

$edit_user = $edit_result->fetch_assoc();
$edit_result = $conn->query("SELECT * FROM users WHERE id=$edit_id");
$edit_user = $edit_result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET username='$username', email='$email', full_name='$full_name', address='$address', phone='$phone', role='$role' WHERE id=$edit_id";
    if ($conn->query($sql)) {
        header("Location: admin_users.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light-blue">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Đăng xuất</a></li>
                <li class="nav-item"><a class="nav-link" href="index_admin.php">View website</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php">Quản lý sản phẩm</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Chỉnh sửa người dùng</h1>
        <form action="edit_user.php" method="POST">
            <input type="hidden" name="edit_id" value="<?php echo $edit_user['id']; ?>"> <!-- edit_id = $edit_user['id'] --->
            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $edit_user['username']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $edit_user['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Họ tên</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $edit_user['full_name']; ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <textarea class="form-control" id="address" name="address"><?php echo $edit_user['address']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $edit_user['phone']; ?>">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Vai trò</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="user" <?php echo $edit_user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $edit_user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="admin_users.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</body>
</html>