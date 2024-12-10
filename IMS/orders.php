<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
require_once '../config/connection.php'; 

$query = "
    SELECT o.id AS order_id, 
           p.name AS product_name, 
           o.quantity, 
           s.name AS supplier_name, 
           o.status, 
           o.delivery_deadline
    FROM orders o
    INNER JOIN products p ON o.product_id = p.id
    INNER JOIN suppliers s ON o.supplier_id = s.id";
$orders = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);


$products = $pdo->query("SELECT id, name FROM products")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

// Add Order logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addOrder'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $supplier_id = $_POST['supplier_id'];
    $status = $_POST['status'];
    $deliveryDeadline = $_POST['deliveryDeadline'];

    $pdo->prepare("
        INSERT INTO orders (product_id, quantity, supplier_id, status, delivery_deadline) 
        VALUES (?, ?, ?, ?, ?)
    ")->execute([$product_id, $quantity, $supplier_id, $status, $deliveryDeadline]);

    header('Location: orders.php');
    exit;
}

// Update Order logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateOrder'])) {
    $id = $_POST['id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $supplier_id = $_POST['supplier_id'];
    $status = $_POST['status'];
    $deliveryDeadline = $_POST['deliveryDeadline'];

    $pdo->prepare("
        UPDATE orders 
        SET product_id = ?, quantity = ?, supplier_id = ?, status = ?, delivery_deadline = ? 
        WHERE id = ?
    ")->execute([$product_id, $quantity, $supplier_id, $status, $deliveryDeadline, $id]);

    header('Location: orders.php');
    exit;
}

// Delete Order logic
if (isset($_GET['deleteOrderID'])) {
    $orderID = $_GET['deleteOrderID'];
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderID]);

    header('Location: orders.php');
    exit;
}
?>
<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Order Management</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="orders.php" class="active">Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="toolbar">
            <input type="text" class="search-bar" placeholder="Search...">
            <button class="btn add-item" onclick="openPopup('addOrderPopup')">Add Order +</button>
        </div>

        <!-- Order Table -->
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Supplier</th>
                    <th>Status</th>
                    <th>Delivery Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']); ?></td>
                        <td><?= htmlspecialchars($order['product_name']); ?></td>
                        <td><?= htmlspecialchars($order['quantity']); ?></td>
                        <td><?= htmlspecialchars($order['supplier_name']); ?></td>
                        <td><?= htmlspecialchars($order['status']); ?></td>
                        <td><?= htmlspecialchars($order['delivery_deadline']); ?></td>
                        <td>
                        <button class="btn edit-btn" onclick="openEditPopup(<?= htmlspecialchars(json_encode($order)); ?>)">Edit</button>
                        <button class="btn delete-btn" onclick="openDeletePopup(<?= $order['order_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Order Popup -->
<div id="addOrderPopup" class="popup">
    <form class="popup-form" method="POST">
        <h3>Add Order</h3>
        <label for="product_id">Product:</label>
        <select id="product_id" name="product_id" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>

        <label for="supplier_id">Supplier:</label>
        <select id="supplier_id" name="supplier_id" required>
            <option value="">Select Supplier</option>
            <?php foreach ($suppliers as $supplier): ?>
                <option value="<?= $supplier['id']; ?>"><?= htmlspecialchars($supplier['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="status">Status:</label>
        <input type="text" id="status" name="status" required>

        <label for="deliveryDeadline">Delivery Deadline:</label>
        <input type="date" id="deliveryDeadline" name="deliveryDeadline" required>

        <div class="popup-buttons">
            <button type="submit" class="btn save" name="addOrder">Add</button>
            <button type="button" class="btn cancel" onclick="closePopup('addOrderPopup')">Cancel</button>
        </div>
    </form>
</div>


<!-- Update/Edit Order Popup -->
<div id="updateOrderPopup" class="popup">
    <form class="popup-form" method="POST">
        <h3>Update Order</h3>
        <!-- Hidden field to store Order ID -->
        <input type="hidden" id="edit_id" name="id">

        <!-- Product Dropdown -->
        <label for="edit_product_id">Product:</label>
        <select id="edit_product_id" name="product_id" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Quantity Input -->
        <label for="edit_quantity">Quantity:</label>
        <input type="number" id="edit_quantity" name="quantity" required>

        <!-- Supplier Dropdown -->
        <label for="edit_supplier_id">Supplier:</label>
        <select id="edit_supplier_id" name="supplier_id" required>
            <option value="">Select Supplier</option>
            <?php foreach ($suppliers as $supplier): ?>
                <option value="<?= $supplier['id']; ?>"><?= htmlspecialchars($supplier['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Status Input -->
        <label for="edit_status">Status:</label>
        <input type="text" id="edit_status" name="status" required>

        <!-- Delivery Deadline -->
        <label for="edit_deliveryDeadline">Delivery Deadline:</label>
        <input type="date" id="edit_deliveryDeadline" name="deliveryDeadline" required>

        <!-- Popup Buttons -->
        <div class="popup-buttons">
            <button type="submit" class="btn save" name="updateOrder">Save</button>
            <button type="button" class="btn cancel" onclick="closePopup('updateOrderPopup')">Cancel</button>
        </div>
    </form>
</div>



<!-- Delete Order Popup -->
<div id="deleteOrderPopup" class="popup">
    <div class="popup-form">
        <h3>Delete Order</h3>
        <p>Are you sure you want to delete this order?</p>
        <div class="popup-buttons">
            <!-- The button dynamically triggers delete logic -->
            <button type="button" class="btn save" id="confirmDeleteButton">Yes, Delete</button>
            <button type="button" class="btn cancel" onclick="closePopup('deleteOrderPopup')">Cancel</button>
        </div>
    </div>
</div>


<script>

// Function to open a popup
function openPopup(popupId) {
    document.getElementById(popupId).style.display = 'block';
}

// Function to close a popup
function closePopup(popupId) {
    document.getElementById(popupId).style.display = 'none';
}

// Function to open the Edit Order popup and populate it with data
function openEditPopup(order) {
    openPopup('updateOrderPopup');

    // Populate the form fields with the selected order details
    document.getElementById('id').value = order.order_id;
    document.getElementById('product_id').value = order.product_id; // Product ID for hidden logic
    document.getElementById('quantity').value = order.quantity;
    document.getElementById('supplier_id').value = order.supplier_id; // Supplier ID for hidden logic
    document.getElementById('status').value = order.status;
    document.getElementById('deliveryDeadline').value = order.delivery_deadline;
}

// Function to open the Delete Confirmation popup
function openDeletePopup(orderID) {
    if (confirm("Are you sure you want to delete this order?")) {
        window.location.href = `orders.php?deleteOrderID=${orderID}`;
    }
}

// Function to open the Update/Edit Popup and populate fields
function openEditPopup(order) {
    openPopup('updateOrderPopup');

    // Populate the form fields with the selected order details
    document.getElementById('edit_id').value = order.order_id;
    document.getElementById('edit_product_id').value = order.product_id; // Ensure this matches your dropdown value
    document.getElementById('edit_quantity').value = order.quantity;
    document.getElementById('edit_supplier_id').value = order.supplier_id; // Ensure this matches your dropdown value
    document.getElementById('edit_status').value = order.status;
    document.getElementById('edit_deliveryDeadline').value = order.delivery_deadline;
}

// Function to open the Delete Popup and bind the order ID
function openDeletePopup(orderId) {
    openPopup('deleteOrderPopup');

    // Dynamically bind the delete logic to the button
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    confirmDeleteButton.onclick = function () {
        window.location.href = `orders.php?deleteOrderID=${orderId}`;
    };
}

// General Function to Open a Popup
function openPopup(popupId) {
    document.getElementById(popupId).style.display = 'block';
}

// General Function to Close a Popup
function closePopup(popupId) {
    document.getElementById(popupId).style.display = 'none';
}

// Function to open the Delete Popup and bind the order ID
function openDeletePopup(orderId) {
    openPopup('deleteOrderPopup'); // Open the popup

    // Dynamically bind the orderId to the delete button's click event
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    
    // Clear any previous onclick handlers to avoid duplication
    confirmDeleteButton.onclick = function () {
        // Redirect to the delete URL with the orderId
        window.location.href = `orders.php?deleteOrderID=${orderId}`;
    };
}

// General Function to Open a Popup
function openPopup(popupId) {
    document.getElementById(popupId).style.display = 'block';
}

// General Function to Close a Popup
function closePopup(popupId) {
    document.getElementById(popupId).style.display = 'none';
}


</script>
</body>
</html>
