<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Password</title>
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

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .input-container {
            position: relative;
            margin-bottom: 15px;
        }

        .input-container input {
            width: 100%;
            padding: 10px 40px 10px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            outline: none;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .toggle-password i {
            font-size: 18px;
            color: #666;
        }

        .toggle-password i:hover {
            color: #333;
        }

        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            cursor: pointer;
            margin-right: 10px;
        }

        .button:hover {
            background-color: #45a049;
        }

        .cancel-button {
            display: inline-block;
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        .cancel-button:hover {
            background-color: #e53935;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Password</h2>
        <div class="input-container">
            <input type="password" id="password" placeholder="Enter new password" autocomplete="off">
            <span class="toggle-password" onclick="togglePasswordVisibility()">
                <i class="fa fa-eye" id="eye-icon"></i>
            </span>
        </div>
        <button class="button" onclick="savePassword()">Save</button>
        <a href="view_sales.php" class="cancel-button">Cancel</a>
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

        function savePassword() {
            const password = document.getElementById('password').value;
            fetch('update_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'password=' + encodeURIComponent(password)
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        alert('Password updated successfully.');
                        window.location.href = 'view_sales.php';
                    } else {
                        alert('Error updating password: ' + data);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
        }
    </script>
</body>

</html>