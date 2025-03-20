<?php
try{
    include 'config.php';
}
catch(Exception $e){
    echo ''.$e->getMessage().'';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    //check user name ton tai hay chua
    $checkUsernameSQL = "SELECT * FROM users WHERE username = ?";
    $cmd = $conn -> prepare($checkUsernameSQL);
    $cmd->bind_param("s", $username); //bind s(string ) -> ? = username
    $cmd->execute();
    $result = $cmd->get_result();
    //param se la 1 tham so chu khong phai 1 phan cua cau lenh sql, sql xu ly duoi dang 1 chuoi an toan
    /* 
    SELECT * FROM users WHERE username = 'admin\' OR \'1\'=\'1';
    nó sẽ tìm một bản ghi có username là admin' OR '1'='1 (chi nhan dau ' o dau va cuoi)
    */

    //check email ton tai hay chua
    $checkEmailSQL = "SELECT * FROM users WHERE email = ?";
    $emailCmd = $conn->prepare($checkEmailSQL);
    $emailCmd->bind_param("s", $email); // bind s (string) -> ? = email
    $emailCmd->execute();
    $emailResult = $emailCmd->get_result();

    if ($result->num_rows > 0) {
        echo"Username existed";
    }

    if ($emailResult->num_rows > 0) {
        echo "Email already exists.<br>";
    }

    else {
        $sql = "INSERT INTO users (username, password, email, full_name, address, phone) 
                VALUES (?, ?, ?, ? ,?,?)";

        $cmd = $conn -> prepare($sql);
        $cmd->bind_param('ssssss', $username, $password, $email, $full_name, $address, $phone);

        if ($cmd->execute()) {
            header("Location: login.php"); //Login sau khi register
            echo "Register success!";
            exit();
        } 
        else {
            echo "Lỗi: " . $conn->error;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Đăng ký</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Họ tên</label>
                <input type="text" class="form-control" id="full_name" name="full_name">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <textarea class="form-control" id="address" name="address"></textarea>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
            <button type="submit" class="btn btn-primary">Đăng ký</button>
        </form>
        <a href="login.php">Login here</a>
    </div>
</body>
</html>