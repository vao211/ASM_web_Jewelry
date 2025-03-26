<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}


if (!isset($_GET['id']) && !isset($_POST['edit_id'])) {
    header("Location: admin.php");
    exit();
}

$edit_id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['edit_id']) ? $_POST['edit_id'] : null);
$edit_result = $conn->query("SELECT * FROM products WHERE id=$edit_id");
$edit_product = $edit_result->fetch_assoc();
$categories = $conn->query("SELECT * FROM categories");

if (!$edit_product) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $current_image = $edit_product['image']; //if main image not changed
    $current_detail_images = !empty($edit_product['detail_image']) ? explode(',', $edit_product['detail_image']) : [];

    //main image
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false && $_FILES["image"]["size"] <= 5000000 && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $current_image = $image_name;
                //delete old main image if exists
                if (file_exists("../uploads/" . $edit_product['image'])) {
                    unlink("../uploads/" . $edit_product['image']);
                }
            }
        }
    }

    //detail images
    $new_detail_images = $current_detail_images; //if detaild image not changed

    //delete detail image
    if (isset($_POST['remove_detail_image'])) {
        $remove_images = $_POST['remove_detail_image'];
        foreach ($remove_images as $img_to_remove) {
            if (($key = array_search($img_to_remove, $new_detail_images)) !== false) {
                unset($new_detail_images[$key]);
                if (file_exists("../" . $img_to_remove)) {
                    unlink("../" . $img_to_remove);
                }
            }
        }
        $new_detail_images = array_values($new_detail_images); //re-index
    }

    //up new detail img
    if (isset($_FILES['detail_images']) && !empty($_FILES['detail_images']['name'][0])) {
        $file_count = count($_FILES['detail_images']['name']);
        if (count($new_detail_images) + $file_count > 4) {
            die("Total number of detail images cannot exceed 4!");
        }

        for ($i = 0; $i < $file_count; $i++) {
            $detail_image_name = uniqid() . '_' . basename($_FILES['detail_images']['name'][$i]);
            $detail_target_file = "../uploads/" . $detail_image_name;
            $detail_imageFileType = strtolower(pathinfo($detail_target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES['detail_images']['tmp_name'][$i]);
            if ($check !== false && $_FILES['detail_images']['size'][$i] <= 200000000 && in_array($detail_imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                if (move_uploaded_file($_FILES['detail_images']['tmp_name'][$i], $detail_target_file)) {
                    $new_detail_images[] = "uploads/" . $detail_image_name;
                }
            }
        }
    }

    $detail_image_string = implode(',', $new_detail_images);

    $sql = "UPDATE products SET name=?, price=?, category_id=?, description=?, stock=?, image=?, detail_image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdisissi", $name, $price, $category_id, $description, $stock, $current_image, $detail_image_string, $edit_id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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
                <li class="nav-item"><a class="nav-link" href="../backend/profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Log out</a></li>
                <li class="nav-item"><a class="nav-link" href="../backend/admin.php">Quit</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Edit Product</h1>
        <form action="edit.php" method="POST" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="edit_id" value="<?php echo $edit_product['id']; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_product['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <?php while ($cat = $categories->fetch_assoc()) { ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $edit_product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price (VND)</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($edit_product['price']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock Quantity</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($edit_product['stock']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image (Leave blank if not changing)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <img src="../uploads/<?php echo htmlspecialchars($edit_product['image']); ?>" width="100" class="mt-2">
            </div>
            <div class="mb-3">
                <label class="form-label">Current Detail Images</label>
                <div>
                    <?php
                    $detail_images = !empty($edit_product['detail_image']) ? explode(',', $edit_product['detail_image']) : [];
                    foreach ($detail_images as $img) {
                        echo '<div class="d-inline-block me-2">';
                        echo '<img src="../' . htmlspecialchars(trim($img)) . '" width="100" class="mb-2">';
                        echo '<div><input type="checkbox" name="remove_detail_image[]" value="' . htmlspecialchars(trim($img)) . '"> Remove</div>';
                        echo '</div>';
                    }
                    if (empty($detail_images)) {
                        echo "No detail images available.";
                    }
                    ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="detail_images" class="form-label">Upload New Detail Images (Max 4 total)</label>
                <input type="file" class="form-control" id="detail_images" name="detail_images[]" accept="image/*" multiple>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($edit_product['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="admin.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>