<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT o.*, od.product_id, od.quantity, od.price, p.name, o.admin_message 
                        FROM orders o 
                        LEFT JOIN order_details od ON o.id = od.order_id 
                        LEFT JOIN products p ON od.product_id = p.id 
                        WHERE o.user_id = ? 
                        ORDER BY o.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
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
        <h1>Order History</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Message</th>
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
                        <td><?php echo $row['admin_message'] ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['name'] . " (x" . $row['quantity'] . ", " . $row['price'] . " VND)"; ?>
                <?php } else { ?>
                        <?php echo "<br>" . $row['name'] . " (x" . $row['quantity'] . ", " . $row['price'] . " VND)"; ?>
                <?php } } if ($current_order !== null) echo "</td></tr>"; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>