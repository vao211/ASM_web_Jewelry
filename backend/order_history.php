<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT o.*, od.product_id, od.quantity, od.price, p.name 
        FROM orders o 
        LEFT JOIN order_details od ON o.id = od.order_id 
        LEFT JOIN products p ON od.product_id = p.id 
        WHERE o.user_id = $user_id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Order History</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Order Date</th>
                    <th>Products</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $current_order = null;
                while ($row = $result->fetch_assoc()) { 
                    if ($current_order != $row['id']) {
                        if ($current_order !== null) echo "</td></tr>";
                        $current_order = $row['id'];
                ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['total']; ?> VND</td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['payment_method']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['name'] . " (x" . $row['quantity'] . ", " . $row['price'] . " VND)"; ?>
                <?php } else { ?>
                        <?php echo "<br>" . $row['name'] . " (x" . $row['quantity'] . ", " . $row['price'] . " VND)"; ?>
                <?php } } if ($current_order !== null) echo "</td></tr>"; ?>
            </tbody>
        </table>
    </div>
</body>
</html>