<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">    
</head>
<style>
    body{
        background-color: #1D5C9A;
        height: 600px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    form{
        width: 400px;
        height: 250px;
        padding: 20px;
        background-color: white;
        font-size: 20px;
        border-radius: 20px;
        gap: 20px;
    }
    h2{
        color: #1D5C9A;
        text-align: center;
    }
    input{
        display: flex;
        gap: 20px;
        margin: 10px;
        width: 84%;
        height: 25px;
        border-radius: 20px;
        padding: 10px;
        border: 1px solid #1D5C9A;
        outline: none;
    }
    button{
        width: 150px;
        height: 35px;
        outline: none;
        border: none;
        border-radius: 20px;
        margin: 10px;
        background-color: #1D5C9A;
        color: white;
    }
</style>
<body>

<?php
session_start();
require_once '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE username = '$username'";  
    $result = $pdo->query($query);
    $admin = $result->fetch();  

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $admin['username'];  
        header('Location: dashboard.php');  
        exit;
    } else {
        
        $error = "Invalid username or password.";
    }
}

$username = 'admin';
$password = 'Aneeqa'; // Set your desired password

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert the new admin into the database (Only run once)
$query = "INSERT INTO admins (username, password) VALUES ('$username', '$hashedPassword')";
$pdo->exec($query);  // This creates the admin in your database



?>


<!-- Login Form -->
<form method="POST" class="login-form">
    <h2>Login</h2>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
</form>
</body>
</html>
