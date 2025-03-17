<?php
include 'config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? $_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? $_GET['max_price'] : PHP_INT_MAX;

$sql = "SELECT * FROM products WHERE name LIKE '%$search%' AND price >= $min_price AND price <= $max_price";
if ($category) {
    $sql .= " AND category_id = $category";
}
$result = $conn->query($sql);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
echo json_encode($products);
?>