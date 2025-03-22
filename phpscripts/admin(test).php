<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/index.html");
    exit();
}

$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
$result = $conn->query($sql);
$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit_id'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];

    $target_dir = "../uploads/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false || $_FILES["image"]["size"] > 5000000 || !in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        die("File ảnh không hợp lệ!");
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (category_id, name, price, image, description, stock) 
                VALUES ('$category_id', '$name', '$price', '$image_name', '$description', '$stock')";

        if ($conn->query($sql)) {
            header("Location: admin.php");
        } 
        else {
            echo "Lỗi: " . $conn->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];

    $sql = "UPDATE products SET name='$name', price='$price', category_id='$category_id', description='$description', stock='$stock'";
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false && $_FILES["image"]["size"] <= 5000000 && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            $sql .= ", image='$image_name'";
        }
    }
    
    $sql .= " WHERE id=$edit_id";
    if ($conn->query($sql)) {
        header("Location: admin.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM products WHERE id=$edit_id");
    $edit_product = $edit_result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
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
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Quản lý sản phẩm</h1>
        <form action="admin.php" method="POST" enctype="multipart/form-data" class="mb-4">

            <?php if ($edit_product) { ?>
                <input type="hidden" name="edit_id" value="<?php echo $edit_product['id']; ?>">
            <?php } ?>

            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_product ? $edit_product['name'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Danh mục</label>
                <select class="form-control" id="category_id" name="category_id" required>

                    <?php while ($cat = $categories->fetch_assoc()) { ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $edit_product && $edit_product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                    <?php } $categories->data_seek(0); ?>

                </select>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Giá (VND)</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Số lượng tồn kho</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $edit_product ? $edit_product['stock'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Ảnh sản phẩm <?php echo $edit_product ? '(Để trống nếu không thay đổi)' : ''; ?></label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" <?php echo $edit_product ? '' : 'required'; ?>>
                <?php if ($edit_product) { ?>
                    <img src="../uploads/<?php echo $edit_product['image']; ?>" width="100" class="mt-2">
                <?php } ?>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description"><?php echo $edit_product ? $edit_product['description'] : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $edit_product ? 'Cập nhật' : 'Thêm sản phẩm'; ?></button>
            <?php if ($edit_product) { ?>
                <a href="admin.php" class="btn btn-secondary">Hủy</a>
            <?php } ?>
        </form>

        <h2>Danh sách sản phẩm</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Ảnh</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td><img src="../uploads/<?php echo $row['image']; ?>" width="50"></td>
                        <td>
                            <a href="admin.php?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Xóa</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>