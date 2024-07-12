<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {

    $id = $_POST['id'];
    $item = $_POST['item'];
    $color = $_POST['color'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $mop = $_POST['mop'];
    $channel = $_POST['channel'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE vanity_sales_2q2024 SET item=?, color=?, size=?, price=?, mop=?, channel=?, stock=?, status=? WHERE id=?");
    $stmt->bind_param('sssdssssi', $item, $color, $size, $price, $mop, $channel, $stock, $status, $id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Sale updated successfully.');
            window.location.href = 'view_sales.php';
        </script>";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
} else {

    $id = $_GET['id'];
    $sql = "SELECT * FROM vanity_sales_2q2024 WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sale</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Edit.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: black;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left,
        .header-right,
        .nav {
            display: flex;
            align-items: center;
        }

        .header-left {
            margin-left: 20px;/
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

        .form-container {
            width: 300px;
            margin-top: 2%;
            background: white;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input[type=text],
        input[type=number],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            text-align: left;
        }

        .radio-group {
            display: flex;
            justify-content: center;
            margin: 10px 0;
        }

        .radio-group label {
            font-weight: normal;
            margin-right: 15px;
            text-align: left;
        }

        input[type=submit],
        .button {
            width: 120px;
            background-color: #4CAF50;
            color: white;
            padding: 14px 0;
            margin: 10px 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        input[type=submit]:hover,
        .button:hover {
            background-color: #45a049;
        }

        .button.cancel {
            background-color: #f44336;
        }

        .button.cancel:hover {
            background-color: #e53935;
        }

        .nav-container {
            text-align: center;
            margin-top: 20px;
        }

        .color-option {
            color: white;
            padding: 10px;
            text-align: left;
        }

        .white {
            background-color: #ffffff;
            color: #000000;
        }

        .black {
            background-color: #000000;
            color: #ffffff;
        }

        .purple {
            background-color: #800080;
            color: #ffffff;
        }


        .edit-btn,
        .delete-btn {
            text-decoration: none;
            color: white;
            padding: 3px 8px;
            font-size: 12px;
            border: none;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #45a049;
        }

        .delete-btn {
            background-color: #f44336;
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
            <a href="graphs.php">Graphs</a>
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
        
        <h2>Edit Sale</h2>

        <form action="edit.php" method="post">

            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

            <label for="item">Item</label>
            <select name="item" id="item">
                <option value="Butterfly" <?php if ($row['item'] == 'Butterfly') echo 'selected'; ?>>Butterfly</option>
                <option value="Bart" <?php if ($row['item'] == 'Bart') echo 'selected'; ?>>Bart</option>
            </select>

            <label for="color">Color</label>
            <select name="color" id="color">
                <option value="White" class="color-option white" <?php if ($row['color'] == 'White') echo 'selected'; ?>>White</option>
                <option value="Black" class="color-option black" <?php if ($row['color'] == 'Black') echo 'selected'; ?>>Black</option>
                <option value="Purple" class="color-option purple" <?php if ($row['color'] == 'Purple') echo 'selected'; ?>>Purple</option>
            </select>

            <label for="size">Size</label>
            <select name="size" id="size">
                <option value="S" <?php if ($row['size'] == 'S') echo 'selected'; ?>>S</option>
                <option value="M" <?php if ($row['size'] == 'M') echo 'selected'; ?>>M</option>
                <option value="L" <?php if ($row['size'] == 'L') echo 'selected'; ?>>L</option>
                <option value="XL" <?php if ($row['size'] == 'XL') echo 'selected'; ?>>XL</option>
                <option value="2XL" <?php if ($row['size'] == '2XL') echo 'selected'; ?>>2XL</option>
            </select>

            <label for="price">Price</label>
            <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" step="0.01">

            <label for="mop">Mode of Payment</label>
            <select name="mop" id="mop">
                <option value="Cash" <?php if ($row['mop'] == 'Cash') echo 'selected'; ?>>Cash</option>
                <option value="Gcash" <?php if ($row['mop'] == 'Gcash') echo 'selected'; ?>>Gcash</option>
                <option value="Maya" <?php if ($row['mop'] == 'Maya') echo 'selected'; ?>>Maya</option>
            </select>

            <label for="channel">Sales Channel</label>
            <select name="channel" id="channel">
                <option value="Referral (Vhann)" <?php if ($row['channel'] == 'Referral (Vhann)') echo 'selected'; ?>>Referral (Vhann)</option>
                <option value="Referral (Klyde)" <?php if ($row['channel'] == 'Referral (Klyde)') echo 'selected'; ?>>Referral (Klyde)</option>
                <option value="Shopee" <?php if ($row['channel'] == 'Shopee') echo 'selected'; ?>>Shopee</option>
                <option value="Tiktok" <?php if ($row['channel'] == 'Tiktok') echo 'selected'; ?>>Tiktok</option>
            </select>

            <label for="stock">Stock</label>
            <select name="stock" id="stock">
                <option value="In Stock" <?php if ($row['stock'] == 'In Stock') echo 'selected'; ?>>In Stock</option>
                <option value="In Production" <?php if ($row['stock'] == 'In Production') echo 'selected'; ?>>In Production</option>
                <option value="No Stock" <?php if ($row['stock'] == 'No Stock') echo 'selected'; ?>>No Stock</option>
            </select>

            <div class="radio-group">
                <label><input type="radio" name="status" value="1" <?php if ($row['status'] == 1) echo 'checked'; ?>> Paid</label>
                <label><input type="radio" name="status" value="0" <?php if ($row['status'] == 0) echo 'checked'; ?>> Unpaid</label>
            </div>

            <div class="nav-container">
                <input type="submit" name="update" value="Update">
                <a href="view_sales.php" class="button cancel">Cancel</a>
            </div>
        </form>
    </div>

</body>

</html>

<?php
$conn->close();
?>