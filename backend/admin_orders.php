<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}


$sql = "
    SELECT o.id, o.user_id, u.username, o.total, o.status, o.created_at, o.payment_method, o.admin_message
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC";
$result = $conn->query($sql);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];
    $message = $_POST['message'] ?? '';

    if ($action === 'accept') {
        $stmt = $conn->prepare("UPDATE orders SET status = 'processing', admin_message = ? WHERE id = ?");
        $stmt->bind_param("si", $message, $order_id);
    } elseif ($action === 'cancel') {
        $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled', admin_message = ? WHERE id = ?");
        $stmt->bind_param("si", $message, $order_id);
    }

    if ($stmt->execute()) {
        header("Location: admin_orders.php");
        exit();
    } else {
        $error = "Error occurs " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order manager</title>
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
                <li class="nav-item"><a class="nav-link" href="admin.php">Product manager</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_users.php">User manager</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Order manager</h1>
        <?php if (isset($error)) { echo "<p class='text-danger'>$error</p>"; } ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Total</th>
                    <th>Payment method</th>
                    <th>Status</th>
                    <th>Message</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo number_format($row['total'], 2); ?> VND</td>
                        <td><?php echo $row['payment_method']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['admin_message']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <?php if ($row['status'] === 'pending') { ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <textarea name="message" class="form-control mb-2" rows="2" placeholder="Message (Option)"></textarea>
                                    <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="cancel">
                                    <textarea name="message" class="form-control mb-2" rows="2" placeholder="Ignore reason"></textarea>
                                    <button type="submit" class="btn btn-danger btn-sm">Ignore</button>
                                </form>
                            <?php } else { ?>
                                <span>Processed!</span>
                            <?php } ?>
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