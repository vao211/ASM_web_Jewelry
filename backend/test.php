<?php
echo "OK";
//login
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
// header("Location: ../frontend/index.html");
?>