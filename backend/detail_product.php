<?php
session_start();
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.html");
    exit();
}

$product_id = $_GET['id'];
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: ../index.html");
    exit();
}

$product = $result->fetch_assoc();
$detail_images = !empty($product['detail_image']) ? explode(',', $product['detail_image']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product details - <?php echo htmlspecialchars($product['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
    <link rel="icon" href="/favicon.png" type="image/x-icon">
</head>
<body>
<div id="notification" class="notification" style="display: none;">notification</div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light-blue">
        <a class="navbar-brand" href="#">Jewelry Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../frontend/user.php">Go Back</a></li>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="row">
            <div class="col-md-6">
                <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid main-image" id="mainImage" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="mt-3 detail-images" id="detailImages">
                    <?php foreach ($detail_images as $img) { ?>
                        <img src="../<?php echo htmlspecialchars(trim($img)); ?>" class="img-thumbnail detail-image" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" alt="Detail Image" onclick="swapImage(this)">
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-6">
                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name'] ?? 'No category'); ?></p>
                <p><strong>Price:</strong> <?php echo number_format($product['price'], 2); ?> VND</p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>Stock Quantity:</strong> <?php echo $product['stock']; ?></p>
                <button type="button" class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>, event)">Add to Cart</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../frontend/js/script.js"></script>
</body>
</html>