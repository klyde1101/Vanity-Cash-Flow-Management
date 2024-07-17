<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db_connect.php';

$errors = [];
$description = '';
$amount = '';
$date = '';
$showPopup = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';


    if (empty($description)) {
        $errors['description'] = "Description is required.";
    }
    if (!is_numeric($amount) || floatval($amount) <= 0) {
        $errors['amount'] = "Please enter a valid amount greater than zero.";
    }
    if (empty($date)) {
        $errors['date'] = "Date is required.";
    }


    if (count($errors) == 0) {


        $sql = "INSERT INTO expenses (date, description, amount, deleted) VALUES (?, ?, ?, 1)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            echo "Error preparing statement: " . mysqli_error($conn);
        } else {

            mysqli_stmt_bind_param($stmt, "ssd", $date, $description, $amount);

            if (mysqli_stmt_execute($stmt)) {
   
                $_SESSION['popup_description'] = $description;
                $_SESSION['popup_amount'] = $amount;
                $_SESSION['popup_date'] = $date;

                $showPopup = true;
    
                $description = '';
                $amount = '';
                $date = '';
            } else {
                echo '<div class="error-message">Error while adding: ' . mysqli_error($conn) . '</div>';
            }


            mysqli_stmt_close($stmt);
        }
    }
}


mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expenses</title>
    <link rel="stylesheet" type="text/css" href="popup-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

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

        .form-container {
            background-color: #fff;
            width: 20%;
            margin: auto;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        h2 {
            text-align: center;
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: black;
        }

        form label,
        .error-message {
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form label {
            margin-bottom: 5px;
        }

        form input[type="text"],
        form input[type="number"],
        form select {
            padding: 10px;
            margin-bottom: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1.2em;
        }

        form .status-container {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        form .status-container label {
            padding: 5px 10px;
            border-radius: 10px;
            margin: 0 5px;
            cursor: pointer;
            transition: all .15s ease-in-out;
        }

        form input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            cursor: pointer;
            font-size: 1.2em;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        form input[type="submit"] {
            background-color: #4cae4c;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
            display: none;
        }


        .nav-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .button {
            background-color: #303030;
            color: white;
            border: none;
            padding: 15px 30px;
            margin: 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button {
            background-color: #555555;
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
        <h2>ADD EXPENSES</h2>


        <div class="nav-container">
            <a href="expenses.php" class="button">VIEW EXPENSES</a>
        </div>

        <!-- Form for adding expenses -->
        <form method="post">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($description); ?>">
            <?php if (isset($errors['description'])) : ?>
                <div class="error-message"><?php echo $errors['description']; ?></div>
            <?php endif; ?>

            <label for="amount">Amount</label>
            <input type="text" id="amount" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
            <?php if (isset($errors['amount'])) : ?>
                <div class="error-message"><?php echo $errors['amount']; ?></div>
            <?php endif; ?>

            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
            <?php if (isset($errors['date'])) : ?>
                <div class="error-message"><?php echo $errors['date']; ?></div>
            <?php endif; ?>
            <br>

            <input type="submit" name="ADD" value="ADD EXPENSE">
        </form>
    </div>

    <script>
        document.querySelector('form').onsubmit = function(event) {
            var hasError = false;
            var amount = document.getElementById('amount').value.trim();
            if (!/^\d+(\.\d{1,2})?$/.test(amount)) {
                document.getElementById('amountError').style.display = 'block';
                hasError = true;
            }

            if (hasError) {
                event.preventDefault();
            }
        };
    </script>

    <?php if ($showPopup && isset($_SESSION['popup_description'], $_SESSION['popup_amount'], $_SESSION['popup_date'])) : ?>
        <div id="popup" class="popup">
            <div class="popup-content">
                <span class="close" onclick="closePopup()">×</span>
                <h2>NEW EXPENSE ADDED</h2>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($_SESSION['popup_description']); ?></p>
                <p><strong>Amount:</strong> <?php echo htmlspecialchars($_SESSION['popup_amount']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($_SESSION['popup_date']); ?></p>
            </div>
        </div>

        <script>
            window.onload = function() {
                // Show the pop-up
                document.getElementById('popup').style.display = 'block';
            };

            function closePopup() {
                // Hide the pop-up
                document.getElementById('popup').style.display = 'none';
                // Clear session variables after displaying the pop-up
                <?php unset($_SESSION['popup_description'], $_SESSION['popup_amount'], $_SESSION['popup_date']); ?>
            }
        </script>
    <?php endif; ?>
</body>

</html>