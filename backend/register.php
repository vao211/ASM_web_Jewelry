<?php
try {
    include 'config.php';
} catch (Exception $e) {
    echo '' . $e->getMessage() . '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];


    $default_avatar = 'avatar/default/default.png';
    $image = $default_avatar; 


    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../avatar/user/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image";
            exit();
        }
        if ($_FILES["image"]["size"] > 200000000) { 
            echo "File to big!";
            exit();
        }
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "Only accept JPG, JPEG, PNG, GIF.";
            exit();
        }

        //Up file vào thư mục avatar/user/
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = "avatar/user/" . $image_name;
        } else {
            echo "Lỗi khi upload ảnh.";
            exit();
        }
    }


    $checkUsernameSQL = "SELECT * FROM users WHERE username = ?";
    $cmd = $conn->prepare($checkUsernameSQL);
    $cmd->bind_param("s", $username);
    $cmd->execute();
    $result = $cmd->get_result();

    $checkEmailSQL = "SELECT * FROM users WHERE email = ?";
    $emailCmd = $conn->prepare($checkEmailSQL);
    $emailCmd->bind_param("s", $email);
    $emailCmd->execute();
    $emailResult = $emailCmd->get_result();

    if ($result->num_rows > 0) {
        echo "Username đã tồn tại.";
    } elseif ($emailResult->num_rows > 0) {
        echo "Email đã tồn tại.";
    } else {
        $sql = "INSERT INTO users (username, password, email, full_name, address, phone, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $cmd = $conn->prepare($sql);
        $cmd->bind_param('sssssss', $username, $password, $email, $full_name, $address, $phone, $image);

        if ($cmd->execute()) {
            header("Location: login.php"); 
            exit();
        } else {
            echo "Lỗi: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">   
    <link rel="icon" href="/favicon.png" type="image/x-icon">
    </head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light-blue">
        <a class="navbar-brand" href="#">Jewelry Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/index.html">Back</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Register</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address"></textarea>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Avatar (Optinal)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <a href="login.php">Login here</a>
    </div>
</body>
</html>