CREATE DATABASE inventory_system;

USE inventory_system;

-- Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    category ENUM('Makeup', 'Skincare', 'Haircare') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL,
    reorder_level INT DEFAULT 0
);

-- Suppliers Table
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    contact VARCHAR(30) NOT NULL,
    product_supplied VARCHAR(100) NOT NULL, 
    order_history TEXT 
);

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    supplier_id INT NOT NULL,                
    status VARCHAR(50) NOT NULL,
    delivery_deadline DATE NOT NULL,         
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);


-- Admin Table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50)  NOT NULL,
    password VARCHAR(70) NOT NULL 
);


