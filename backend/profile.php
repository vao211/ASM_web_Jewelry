<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $image = $user['image'];

    //up avatar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../avatar/user/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "File không phải là ảnh.";
            exit();
        }
        if ($_FILES["image"]["size"] > 200000000) { 
            echo "File ảnh quá lớn.";
            exit();
        }
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "Chỉ chấp nhận file JPG, JPEG, PNG hoặc GIF.";
            exit();
        }

        //up avatar/user/
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = "avatar/user/" . $image_name;
            //del avatar cu neu ko phai default
            if ($user['image'] !== 'avatar/default/default.png' && file_exists("../" . $user['image'])) {
                unlink("../" . $user['image']);
            }
        } else {
            echo "Error occur when upload image";
            exit();
        }
    }

    $sql = "UPDATE users SET full_name = ?, address = ?, phone = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $full_name, $address, $phone, $image, $user_id);

    if ($stmt->execute()) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
                <a class="nav-link" href="/frontend/user.php">Back</a>
            </li>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            <?php } else { ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
            <?php } ?>
        </ul>
    </div>
</nav>

    <div class="container mt-5">
        <h1>Personal Profile</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Avatar</label><br>
                <img src="../<?php echo htmlspecialchars($user['image']); ?>" alt="Avatar" width="100" class="rounded">
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Avatar (Optional)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
<!-- B74p4)T0z+6X^L=VSw%7 -->
</html>