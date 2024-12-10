<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    
</body>
</html>

<?php
session_start();
require_once '../config/connection.php';

//  Add Product
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $reorder_level = $_POST['reorder_level'];

    
    // Insert into database
    $pdo->prepare("INSERT INTO products (name, category, price, stock_quantity, reorder_level) VALUES (?, ?, ?, ?, ?)")
        ->execute([$name, $category, $price, $stock_quantity, $reorder_level, ]);
    header('Location: inventory.php');
    exit;
}

//  Edit Product
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $reorder_level = $_POST['reorder_level'];

    // Update product in database
    $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock_quantity = ?, reorder_level = ? WHERE id = ?")
    ->execute([$name, $category, $price, $stock_quantity, $reorder_level, $id]);
    header('Location: inventory.php');
    exit;
}

//  Delete Product
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];

    // Delete product from database
    $pdo->prepare("DELETE FROM products WHERE id = ?")
        ->execute([$id]);
    header('Location: inventory.php');
    exit;
}

// Fetch products
$query = "SELECT * FROM products";
$products = $pdo->query($query)->fetchAll();
?>


    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Inventory</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="inventory.php" class="active">Inventory</a></li>
                <li><a href="suppliers.php">Suppliers</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2>Inventory</h2>

            <!-- Toolbar -->
            <div class="toolbar">
                <input type="text" placeholder="Search..." class="search-bar">
                <button class="btn add-item" onclick="openAddPopup()">Add Item +</button>
            </div>

            <!-- Product Table -->
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Reorder Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?= $product['name'] ?>
                            </td>
                            <td><?= $product['category'] ?></td>
                            <td><?= $product['price'] ?></td>
                            <td><?= $product['stock_quantity'] ?></td>
                            <td><?= $product['reorder_level'] ?></td>
                            <td>
                                <button class="btn edit-btn" onclick="openEditPopup(<?= htmlspecialchars(json_encode($product)) ?>)">Edit</button>
                                <button class="btn delete-btn" onclick="openDeletePopup(<?= $product['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Product Popup -->
    <div class="popup" id="addProductPopup">
        <form action="" method="POST" enctype="multipart/form-data" class="popup-form">
            <h3>Add Product</h3>
            <input type="hidden" name="action" value="add">
            <label>Product Name:</label>
            <input type="text" name="name" required>
            <label>Category:</label>
            <select name="category" required>
                <option value="Makeup">Makeup</option>
                <option value="Skincare">Skincare</option>
                <option value="Haircare">Haircare</option>
            </select>
            <label>Price:</label>
            <input type="number" name="price" step="0.01" required>
            <label>Stock Quantity:</label>
            <input type="number" name="stock_quantity" required>
            <label>Reorder Level:</label>
            <input type="number" name="reorder_level" required>
            <button type="submit" class="btn save">Save</button>
            <button type="button" class="btn cancel" onclick="closePopup('addProductPopup')">Cancel</button>
        </form>
    </div>

    <!-- Edit Product Popup -->
    <div class="popup" id="editProductPopup">
        <form action="" method="POST" enctype="multipart/form-data" class="popup-form">
            <h3>Edit Product</h3>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editProductId">
            <label>Product Name:</label>
            <input type="text" name="name" id="editProductName" required>
            <label>Category:</label>
            <select name="category" id="editProductCategory" required>
                <option value="Makeup">Makeup</option>
                <option value="Skincare">Skincare</option>
                <option value="Haircare">Haircare</option>
            </select>
            <label>Price:</label>
            <input type="number" name="price" id="editProductPrice" step="0.01" required>
            <label>Stock Quantity:</label>
            <input type="number" name="stock_quantity" id="editProductQuantity" required>
            <label>Reorder Level:</label>
            <input type="number" name="reorder_level" id="editProductReorder" required>
            
            <input type="hidden" name="existing_image" id="editProductExistingImage">
            <button type="submit" class="btn save">Save</button>
            <button type="button" class="btn cancel" onclick="closePopup('editProductPopup')">Cancel</button>
        </form>
    </div>

    <!-- Delete Product Popup -->
    <div class="popup" id="deleteProductPopup">
        <form action="" method="POST" class="popup-form">
            <h3>Are you sure you want to delete this product?</h3>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deleteProductId">
            <button type="submit" class="btn delete">Delete</button>
            <button type="button" class="btn cancel" onclick="closePopup('deleteProductPopup')">Cancel</button>
        </form>
    </div>



 <script>
         function closePopup(popupId) {
         document.getElementById(popupId).classList.remove('active');
        }


         function openAddPopup() {
        document.getElementById('addProductPopup').style.display = 'block';
        }    

        function openEditPopup(product) {
        document.getElementById('editProductId').value = product.id;
        document.getElementById('editProductName').value = product.name;
        document.getElementById('editProductCategory').value = product.category;
        document.getElementById('editProductPrice').value = product.price;
        document.getElementById('editProductQuantity').value = product.stock_quantity;
        document.getElementById('editProductReorder').value = product.reorder_level;
        document.getElementById('editProductPopup').style.display = 'block';
        }

        function openDeletePopup(id) {
        document.getElementById('deleteProductId').value = id;
        document.getElementById('deleteProductPopup').style.display = 'block';
        }

        function closePopup(popupId) {
        document.getElementById(popupId).style.display = 'none';
        }
    </script>
    
</body>
</html>

