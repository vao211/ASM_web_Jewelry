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
    <div class="container mt-5 text-center">
        <?php if (!isset($order_id)) { ?>
            <h1>Chọn phương thức thanh toán</h1>
            <form method="POST">
                <div class="mb-3">
                    <label><input type="radio" name="payment_method" value="credit_card" checked> Thẻ tín dụng</label><br>
                    <label><input type="radio" name="payment_method" value="e_wallet"> Ví điện tử</label><br>
                    <label><input type="radio" name="payment_method" value="bank_transfer"> Chuyển khoản</label>
                </div>
                <button type="submit" class="btn btn-primary">Tiếp tục</button>
            </form>
        <?php } else { ?>
            <h1>Quét mã QR để thanh toán</h1>
            <img src="qr.png" alt="QR Code">
            <br><br>
            <button onclick="alert('Thanh toán thành công!'); window.location.href='../frontend/index.html'" class="btn btn-primary">OK</button>
        <?php } ?>
    </div>
</body>
</html>