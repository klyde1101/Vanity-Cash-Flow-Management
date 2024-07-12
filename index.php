<?php
session_start();

$db_host = "zhw.h.filess.io";
$db_user = "VanitySales_donkeysang";
$db_pass = "385dd18585e19524b017f3035d940465c5c927d6";
$db_name = "VanitySales_donkeysang";
$db_port = "3305";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        if ($stored_password === '123' || password_verify('123', $stored_password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE username = ?");
            $update_stmt->bind_param("ss", $hashed_password, $username);
            $update_stmt->execute();
            $update_stmt->close();
            $stored_password = $hashed_password;
        }

        if (password_verify($password, $stored_password)) {
            $_SESSION['username'] = $username;
            header("Location: home.php");
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            max-width: 80%;
        }

        .login-container h1 {
            margin-bottom: 20px;
            color: black;
            text-align: center;
        }

        .login-form {
            display: flex;
            flex-direction: column;
        }

        .login-form label {
            margin-bottom: 10px;
            color: black;
            font-weight: bold;
            align-self: flex-start;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input {
            padding-right: 40px;
        }

        .password-container .toggle-password {
            position: absolute;
            right: 10px;
            top: 55%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .password-container .toggle-password i {
            font-size: 18px;
            color: #666;
        }

        .password-container .toggle-password i:hover {
            color: #333;
        }

        .login-form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-form button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>VANITY</h1>
        <form class="login-form" method="post" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <div class="password-container">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i class="fa fa-eye" id="eye-icon"></i>
                </span>
            </div>
            <button type="submit">Login</button>
        </form>
        <?php
        if (!empty($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>