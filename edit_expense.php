<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE expenses SET description=?, amount=?, date=? WHERE id=?");
    $stmt->bind_param('sdsi', $description, $amount, $date, $id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Expense updated successfully.');
            window.location.href = 'expenses.php';
        </script>";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM expenses WHERE id=?";
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
    <title>Edit Expense</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Edit.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;

        }

        .form-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
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
        <h2>Edit Expense</h2>
        <form action="edit_expense.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" required>

            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" value="<?php echo htmlspecialchars($row['amount']); ?>" step="0.01" required>

            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($row['date']); ?>" required>

            <div class="nav-container">
                <input type="submit" name="update" value="Update">
                <a href="expenses.php" class="button cancel">Cancel</a>
            </div>
        </form>
    </div>

</body>

</html>