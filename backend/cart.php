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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Giỏ hàng</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Tên</th>
                    <th>Giá</th>
                    <th>Ảnh</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><img src="../uploads/<?php echo $row['image']; ?>" width="50"></td>
                        <td><a href="cart.php?delete=<?php echo $row['cart_id']; ?>" class="btn btn-danger btn-sm">Xóa</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="payment.php" class="btn btn-primary">Thanh toán</a>
    </div>
</body>
</html>
<?php
if (isset($_GET['delete'])) {
    $cart_id = $_GET['delete'];
    $conn->query("DELETE FROM cart WHERE id = $cart_id AND user_id = " . $_SESSION['user_id']);
    header("Location: cart.php");
}
?>