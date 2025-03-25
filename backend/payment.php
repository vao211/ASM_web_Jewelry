<?php
session_start();
include 'config.php';
require_once 'phpqrcode/qrlib.php'; //

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total = 0;
$sql = "SELECT p.price, p.id AS product_id, c.quantity 
        FROM cart c JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $total += $row['price'] * $row['quantity'];
    $cart_items[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    
    $sql = "INSERT INTO orders (user_id, total, payment_method) VALUES ($user_id, $total, '$payment_method')";
    $conn->query($sql);
    $order_id = $conn->insert_id;

    foreach ($cart_items as $item) {
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})";
        $conn->query($sql);
    }

    $conn->query("DELETE FROM cart WHERE user_id = $user_id");

    $qr_content = "Thanh toán: $total VND\nOrder ID: $order_id\nPhương thức: $payment_method";
    QRcode::png($qr_content, "qr.png");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
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
                    <a class="nav-link" href="checkout.php">Back</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <?php if (!isset($order_id)) { ?>
            <h1>Choose payment method</h1>
            <form method="POST">
                <div class="mb-3">
                    <label><input type="radio" name="payment_method" value="credit_card" checked>Credit card</label><br>
                    <label><input type="radio" name="payment_method" value="e_wallet">E-Wallet</label><br>
                    <label><input type="radio" name="payment_method" value="bank_transfer">Bank transfer</label>
                </div>
                <button type="submit" class="btn btn-primary">Go</button>
            </form>
        <?php } else { ?>
            <h1>Scan QR code to pay</h1>
            <img src="qr.png" alt="QR Code">
            <br><br>
            <button onclick="alert('Payment Successful!'); window.location.href='../frontend/user.php'" class="btn btn-primary">OK</button>
        <?php } ?>
    </div>
</body>
</html>