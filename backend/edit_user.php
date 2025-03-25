<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

//_POST trong form
if (!isset($_GET['id']) && !isset($_POST['edit_id'])) {
    header("Location: admin_users.php");
    exit();
}

if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];
} 
elseif (isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
}
else {
    $edit_id = null;
}

//check exist and valid id
if ($edit_id <= 0) {
    echo "Invalid user ID!";
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stm = $conn->prepare($sql);
$stm->bind_param("i", $edit_id);
$stm->execute();
$edit_result = $stm->get_result();

if ($edit_result->num_rows == 0) {
    echo "User does not exist!";
    exit();
}

$edit_user = $edit_result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, address = ?, phone = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("ssssssi", $username, $email, $full_name, $address, $phone, $role, $edit_id);

    if ($stmt->execute()) {
        header("Location: admin_users.php");
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
    <title>User editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light-blue">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Log out</a></li>
                <li class="nav-item"><a class="nav-link" href="index_admin.php">View website</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php">Product manager</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>User editor</h1>
        <form action="edit_user.php" method="POST">
    <input type="hidden" name="edit_id" value="<?php echo $edit_user['id']; ?>"> <!-- edit_id = $edit_user['id'] --->
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?php echo $edit_user['username']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $edit_user['email']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="full_name" class="form-label">Full Name</label>
        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $edit_user['full_name']; ?>">
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea class="form-control" id="address" name="address"><?php echo $edit_user['address']; ?></textarea>
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $edit_user['phone']; ?>">
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select class="form-control" id="role" name="role" required>
            <option value="user" <?php echo $edit_user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
            <option value="admin" <?php echo $edit_user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="admin_users.php" class="btn btn-secondary">Cancel</a>
</form>
    </div>
</body>
</html>