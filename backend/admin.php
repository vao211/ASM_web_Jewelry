<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
$result = $conn->query($sql);

$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];

    $target_dir = "../uploads/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . uniqid() . '_' . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // main image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false || $_FILES["image"]["size"] > 200000000 || !in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        die("Invalid main image file!");
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $main_image = basename($target_file);
    } else {
        die("Error occurs when uploading files");
    }

    $detail_images = [];
    if (isset($_FILES['detail_images']) && !empty($_FILES['detail_images']['name'][0])) {
        $file_count = count($_FILES['detail_images']['name']);
        if ($file_count > 4) {
            die("Max 4 files!");
        }

        for ($i = 0; $i < $file_count; $i++) {
            $detail_image_name = uniqid() . '_' . basename($_FILES['detail_images']['name'][$i]);
            $detail_target_file = $target_dir . $detail_image_name;
            $detail_imageFileType = strtolower(pathinfo($detail_target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES['detail_images']['tmp_name'][$i]);
            if ($check === false || $_FILES['detail_images']['size'][$i] > 200000000 || !in_array($detail_imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                die("Invalid detail image file $i!");
            }

            if (move_uploaded_file($_FILES['detail_images']['tmp_name'][$i], $detail_target_file)) {
                $detail_images[] = 'uploads/' . $detail_image_name; 
            } else {
                die("Error when uploading detail image $i!");
            }
        }
    }
    // split by ,
    $detail_image_string = implode(',', $detail_images);
    $sql = "INSERT INTO products (category_id, name, price, image, description, stock, detail_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdssis", $category_id, $name, $price, $main_image, $description, $stock, $detail_image_string);

    if ($stmt->execute()) {
        header("Location: admin.php");
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
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
    <link rel="icon" href="/favicon.png" type="image/x-icon">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light-blue">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Sign out</a></li>
                <li class="nav-item"><a class="nav-link" href="index_admin.php">View website</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_users.php">User manager</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_orders.php">Order manager</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Product Manager</h1>
        <form action="admin.php" method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <?php while ($cat = $categories->fetch_assoc()) { ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php } $categories->data_seek(0); ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Cost (VND)</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stocks</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Main Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="detail_images" class="form-label">Detail Images (Max 4 images)</label>
                <input type="file" class="form-control" id="detail_images" name="detail_images[]" accept="image/*" multiple>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>

        <h2>List of Products</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Cost</th>
                    <th>Stocks</th>
                    <th>Image</th>
                    <th>Detail Images</th>
                    <th>Description</th>
                    <th>Action</th>
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
                            <?php
                            if (!empty($row['detail_image'])) {
                                $detail_images = explode(',', $row['detail_image']);
                                foreach ($detail_images as $img) {
                                    echo '<img src="../' . htmlspecialchars(trim($img)) . '" width="50" class="me-2">';
                                }
                            } else {
                                echo "No Detail images";
                            }
                            ?>
                        </td>
                        <td><?php echo $row['description']; ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>