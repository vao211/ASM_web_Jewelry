<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập!']);
        exit();
    }
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $product_id = $data['product_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO cart (user_id, product_id) VALUES ($user_id, $product_id)";
    $conn->query($sql);
    echo json_encode(['status' => 'success']);
    exit();
}


$sql = "SELECT p.*, c.id AS cart_id FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = " . $_SESSION['user_id'];
$result = $conn->query($sql);
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
$cart_count = count($cart_items); 


if (isset($_GET['delete'])) {
    $cart_id = $_GET['delete'];
    $conn->query("DELETE FROM cart WHERE id = $cart_id AND user_id = " . $_SESSION['user_id']);
    header("Location: cart.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
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
                    <a class="nav-link" href="../frontend/user.php">Back</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Cart</h1>
        <?php if ($cart_count == 0) { ?>
            <p>Your cart is empty!</p>
        <?php } else { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Cost</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $row) { ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo number_format($row['price']); ?> VND</td>
                            <td><img src="../uploads/<?php echo $row['image']; ?>" width="50"></td>
                            <td><a href="cart.php?delete=<?php echo $row['cart_id']; ?>" class="btn btn-danger btn-sm">Remove</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <a href="checkout.php?checkout=true" class="btn btn-primary">Payment</a>
        <?php } ?>
    </div>
</body>
</html>