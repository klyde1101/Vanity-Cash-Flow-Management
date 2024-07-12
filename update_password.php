<?php
session_start();

// Include database connection file
require_once 'db_connect.php';

// Function to Hash Password
function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $newPassword = $_POST['password'];

    // Hash the new password
    $hashedPassword = hashPassword($newPassword);


    if ($conn) {
        // Update password in the database
        $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashedPassword, $username);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }


        $stmt->close();
    } else {
        echo 'Database connection failed';
    }
}

// Close the connection
$conn->close();
