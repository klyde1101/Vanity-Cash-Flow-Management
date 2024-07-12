<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vanity ADD Sales</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="AddSales.css">
    <link rel="stylesheet" type="text/css" href="popup-styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
            text-align: center;
        }


        .form-container {
            margin-top: 10px;
            transform: scale(0.8);
            width: 80%;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }


        .popup {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .popup-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            cursor: pointer;
        }

        .header {
            background-color: black;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
            overflow-x: hidden;
        }

        .header-left,
        .header-right,
        .nav {
            display: flex;
            align-items: center;
        }

        .header-left {
            margin-left: 20px;
        }

        .header-left img {
            height: 50px;
            margin-right: 20px;
        }

        .nav {
            flex-grow: 1;
            justify-content: flex-start;
        }

        .nav a {
            background-color: transparent;
            color: white;
            border: none;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
            font-size: 18px;
            font-weight: 300;
            margin-left: 50px;
            transition: background-color 0.3s ease, color 0.3s ease;
            text-decoration: none;
        }

        .nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #f0f0f0;
        }

        .header-right {
            margin-right: 30px;
            justify-content: flex-end;
        }

        .header-right span {
            margin-right: 10px;
        }

        .header-right button {
            margin-left: 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-password-btn {
            background-color: #4682B4;
            color: white;
            border: none;
            padding: 8px 15px;
        }

        .edit-password-btn:hover {
            background-color: #5a9bd4;
        }

        .logout-btn {
            background-color: #DC143C;
            color: white;
            border: none;
            padding: 8px 15px;
        }

        .logout-btn:hover {
            background-color: #C41230;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-left">
            <img src="vnty_logo.png" alt="Vanity TOTAL Sales">
        </div>
        <nav class="nav">
            <a href="home.php">Home</a>
            <a href="view_sales.php">Sales</a>
            <a href="add_sales.php">Add Sales</a>
            <a href="expenses.php">Expenses</a>
        </nav>
        <div class="header-right">
            <?php if (isset($_SESSION['username'])) : ?>
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <button class="edit-password-btn" onclick="window.location.href='edit_password.php'">Change Password</button>
                <form id="logoutForm" action="logout.php" method="post" style="display: inline;">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            <?php else : ?>
                <button onclick="window.location.href='login.php'">Login</button>
            <?php endif; ?>
        </div>
    </header>

    <div class="form-container">
        <h2>VANITY SALES</h2>

        <?php
        $errors = [];
        $showPopup = false;

        if (isset($_POST['ADD'])) {
            $item = $_POST['item'] ?? '';
            $price = $_POST['price'] ?? '';
            $color = $_POST['color'] ?? '';
            $size = $_POST['size'] ?? '';
            $mop = $_POST['mop'] ?? '';
            $channel = $_POST['channel'] ?? '';
            $stock = $_POST['stock'] ?? '';
            $status = $_POST['status'] ?? '';


            if (empty($item)) {
                $errors['item'] = "Item name is required.";
            }
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
                $errors['price'] = "Please enter a valid price.";
            }
            if (empty($color)) {
                $errors['color'] = "Color selection is required.";
            }


            if (count($errors) == 0) {
                include 'db_connect.php';

                $sql = "INSERT INTO vanity_sales_2q2024 (id, item, color, size, price, mop, channel, stock, status, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);

                mysqli_stmt_bind_param($stmt, "sssissis", $item, $color, $size, $price, $mop, $channel, $stock, $status);

                if (mysqli_stmt_execute($stmt)) {
                    $showPopup = true;
                    // Get the current date and time in the format for a datetime column
                    $currentDateTime = date('Y-m-d H:i:s');
                    // Insert the current date and time into the database
                    $sql = "UPDATE vanity_sales_2q2024 SET date = ? WHERE id = LAST_INSERT_ID()";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "s", $currentDateTime);
                    mysqli_stmt_execute($stmt);
                } else {
                    echo "Error while adding: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            }
        }


        if ($showPopup) :
        ?>

            <div id="popup" class="popup">
                <div class="popup-content">
                    <span class="close" onclick="closePopup()">×</span>
                    <h2>NEW SALE</h2>
                    <p><strong>Item:</strong> <span id="popup-item"><?php echo htmlspecialchars($item); ?></span></p>
                    <p><strong>Color:</strong> <span id="popup-color"><?php echo htmlspecialchars($color); ?></span></p>
                    <p><strong>Size:</strong> <span id="popup-size"><?php echo htmlspecialchars($size); ?></span></p>
                    <p><strong>Price: </strong> <span id="popup-price"><?php echo htmlspecialchars($price); ?></span></p>
                </div>
            </div>

            <script>
                window.onload = function() {

                    document.getElementById('popup').style.display = 'block';
                };

                function closePopup() {

                    document.getElementById('popup').style.display = 'none';
                }
            </script>

        <?php endif; ?>

        <form method="post" id="itemForm">

            <br>
            <label for="item">Item Name</label>
            <select name="item" id="item">
                <option value="" selected disabled class="placeholder">Item</option>
                <option value="Butterfly">Butterfly</option>
                <option value="Bart">Bart</option>
            </select>
            <?php if (isset($errors['item'])) : ?>
                <div class="error-message"><?php echo $errors['item']; ?></div>
            <?php endif; ?>
            <br>

            <label for="price">Price</label>
            <input type="number" id="price" name="price" placeholder="Price" step="0.01">
            <?php if (isset($errors['price'])) : ?>
                <div class="error-message"><?php echo $errors['price']; ?></div>
            <?php endif; ?>
            <br>

            <div class="radio-inputs">
                <label class="radio white">
                    <input type="radio" name="color" value="White">
                    <span class="name">White</span>
                </label>
                <label class="radio black">
                    <input type="radio" name="color" value="Black">
                    <span class="name">Black</span>
                </label>
                <label class="radio purple">
                    <input type="radio" name="color" value="Purple">
                    <span class="name">Purple</span>
                </label>
            </div>
            <?php if (isset($errors['color'])) : ?>
                <div class="error-message"><?php echo $errors['color']; ?></div>
            <?php endif; ?>
            <br>

            <label for="size">Size</label>
            <select name="size" id="size">
                <option value="" selected disabled class="placeholder">Size</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
                <option value="2XL">2XL</option>
            </select>
            <br>

            <label for="mop">Mode of Payment</label>
            <select name="mop" id="mop">
                <option value="" selected disabled class="placeholder">Mode of Payment</option>
                <option value="Cash">Cash</option>
                <option value="Gcash">Gcash</option>
                <option value="Maya">Maya</option>
            </select>
            <br>

            <label for="channel">Sales Channel</label>
            <select name="channel" id="channel">
                <option value="" selected disabled class="placeholder">Sales Channel</option>
                <option value="Referral (Vhann)">Referral (Vhann)</option>
                <option value="Referral (Klyde)">Referral (Klyde)</option>
                <option value="Shopee">Shopee</option>
                <option value="Tiktok">Tiktok</option>
            </select>
            <br>

            <label for="stock">Stock</label>
            <select name="stock" id="stock">
                <option value="" selected disabled class="placeholder">Stock</option>
                <option value="In Stock">In Stock</option>
                <option value="In Production">In Production</option>
                <option value="No Stock">No Stock</option>
            </select>
            <br>
            <br>

            <div class="radio-group">
                <label><input type="radio" name="status" value="1"> Paid</label>
                <label><input type="radio" name="status" value="0"> Unpaid</label>
            </div>
            <br>

            <input type="submit" name="ADD" value="ADD SALE">

        </form>
    </div>

    <script>
        document.getElementById('itemForm').onsubmit = function(event) {
            var hasError = false;

            if (document.getElementById('price').value === '') {
                document.getElementById('priceError').style.display = 'block';
                hasError = true;
            }

            if (hasError) {
                event.preventDefault();
            }
        };
    </script>

</body>

</html>