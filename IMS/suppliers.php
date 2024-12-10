<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>

<?php
require_once '../config/connection.php';

// Fetch suppliers from the database
$query = "SELECT * FROM suppliers";
$suppliers = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Handle Add Supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addSupplier'])) {
    $supplierName = $_POST['supplierName'];
    $contactInfo = $_POST['contactInfo'];
    $productSupplied = $_POST['product_supplied'];
    $orderHistory = $_POST['order_history'];

    $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact, product_supplied, order_history) VALUES (?, ?, ?, ?)");
    $stmt->execute([$supplierName, $contactInfo, $productSupplied, $orderHistory]);

    header('Location: suppliers.php');
    exit;
}

// Handle Edit Supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editSupplier'])) {
    $id = $_POST['id'];
    $supplierName = $_POST['editSupplierName'];
    $contactInfo = $_POST['editContactInfo'];
    $productSupplied = $_POST['editProductSupplied'];
    $orderHistory = $_POST['editOrderHistory'];

    $stmt = $pdo->prepare("UPDATE suppliers SET name = ?, contact = ?, product_supplied = ?, order_history = ? WHERE id = ?");
    $stmt->execute([$supplierName, $contactInfo, $productSupplied, $orderHistory, $id]);

    header('Location: suppliers.php');
    exit;
}

// Handle Delete Supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteSupplier'])) {
    $id = $_POST['id'];

    $query = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $query->execute([$id]);

    header('Location: suppliers.php');
    exit;
}

?>


<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Supplier</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="supplier.php" class="active">Supplier</a></li>
            <li><a href="orders.php">Order</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
    <!-- Toolbar -->
    <div class="toolbar">
        <input type="text" class="search-bar" placeholder="Search...">
        <button class="btn add-item" onclick="openPopup('addSupplierPopup')">Add Supplier +</button>
        
    </div>

    <!-- Supplier Table -->
    <table>
        <thead>
            <tr>
                <th>Supplier Name</th>
                <th>Contact Info</th>
                <th>Product Supplied</th>
                <th>Order History</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?= htmlspecialchars($supplier['name']); ?></td>
                    <td><?= htmlspecialchars($supplier['contact']); ?></td>
                    <td><?= htmlspecialchars($supplier['product_supplied']); ?></td>
                    <td><?= htmlspecialchars($supplier['order_history']); ?></td>
                    <td>
                        <button class="btn edit-btn" onclick="openEditPopup(<?= htmlspecialchars(json_encode($supplier), ENT_QUOTES, 'UTF-8') ?>)">Edit</button>
                        <button class="btn delete-btn" onclick="openDeletePopup(<?= $supplier['id'] ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Popups -->

<!-- Add Supplier Popup -->
<div id="addSupplierPopup" class="popup">
    <form action="" method="POST" enctype="multipart/form-data" class="popup-form">
        <h3>Add Supplier</h3>
        <label for="supplierName">Supplier Name:</label>
        <input type="text" id="supplierName" name="supplierName" required>
        <label for="contactInfo">Contact Info:</label>
        <input type="text" id="contactInfo" name="contactInfo" required>
        <label for="product_supplied">Products Supplied:</label>
        <input type="text" id="product_supplied" name="product_supplied" required>
        <label for="products">Order History:</label>
        <input type="text" id="order_history" name="order_history" required>
        <input type="hidden" name="addSupplier" value="1">
        <div class="popup-buttons">
            <button type="submit" class="btn save">Save</button>
            <button type="button" class="btn cancel" onclick="closePopup('addSupplierPopup')">Cancel</button>
        </div>
    </form>
</div>

<!-- Edit Supplier Popup -->
<div id="editSupplierPopup" class="popup">
    <form action="suppliers.php" method="POST" enctype="multipart/form-data" class="popup-form">
        <h3>Edit Supplier</h3>
        <input type="hidden" name="id" id="editSupplierId">
        <label for="editSupplierName">Supplier Name:</label>
        <input type="text" id="editSupplierName" name="editSupplierName" required>
        <label for="editContactInfo">Contact Info:</label>
        <input type="text" id="editContactInfo" name="editContactInfo" required>
        <label for="editProductSupplied">Products Supplied:</label>
        <input type="text" id="editProductSupplied" name="editProductSupplied" required>
        <label for="editOrderHistory">Order History:</label>
        <input type="text" id="editOrderHistory" name="editOrderHistory" required>
        <input type="hidden" name="editSupplier" value="1">
        <div class="popup-buttons">
            <button type="submit" class="btn save">Update</button>
            <button type="button" class="btn cancel" onclick="closePopup('editSupplierPopup')">Cancel</button>
        </div>
    </form>
</div>

<!-- Delete Supplier Popup -->
<div id="deleteSupplierPopup" class="popup">
    <div class="popup-form">
        <h3>Delete Supplier</h3>
        <p>Are you sure you want to delete this supplier?</p>
        <form action="suppliers.php" method="POST">
    <input type="hidden" name="id" id="deleteSupplierId">
    <input type="hidden" name="deleteSupplier" value="1">
    <div class="popup-buttons">
        <button type="submit" class="btn save">Yes, Delete</button>
        <button type="button" class="btn cancel" onclick="closePopup('deleteSupplierPopup')">Cancel</button>
    </div>
</form>

    </div>
</div>


<script>
    function openPopup(id) {
    document.getElementById(id).style.display = 'block';
}

function closePopup(id) {
    document.getElementById(id).style.display = 'none';
}

function openEditPopup(supplier) {
    document.getElementById('editSupplierId').value = supplier.id;
    document.getElementById('editSupplierName').value = supplier.name;
    document.getElementById('editContactInfo').value = supplier.contact;
    document.getElementById('editProductSupplied').value = supplier.product_supplied;
    document.getElementById('editOrderHistory').value = supplier.order_history;

    openPopup('editSupplierPopup');
}

function openDeletePopup(id) {
    document.getElementById('deleteSupplierId').value = id;
    openPopup('deleteSupplierPopup');
}

</script>

</body>
</html>
