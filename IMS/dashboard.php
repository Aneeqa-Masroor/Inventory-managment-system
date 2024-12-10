<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .sidebar {
    width: 20%;
    float: left;
    background: #1D5C9A;
    color: white;
    padding: 15px;
    height: 100vh;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin: 15px 0;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
}

.main-content {
    margin-left: 20%;
    padding: 20px;
} 

 table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    border: 1px solid #ddd;
    padding: 8px;
} 

</style>
</head>
<body>
    

    


<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/connection.php';

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalSuppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
?>

<div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Welcome, <?= $_SESSION['admin'] ?>!</h1>
        <p>Here are the key statistics of the system:</p>

        <div class="dashboard-metrics">
            <div class="metric">
                <h3>Total Products</h3>
                <p><?= $totalProducts ?></p>
            </div>
            <div class="metric">
                <h3>Total Suppliers</h3>
                <p><?= $totalSuppliers ?></p>
            </div>
            <div class="metric">
                <h3>Pending Orders</h3>
                <p><?= $pendingOrders ?></p>
            </div>
        </div>
    </div>
</body>
</html>