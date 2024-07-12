<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$db_host = "zhw.h.filess.io";
$db_user = "VanitySales_donkeysang";
$db_pass = "385dd18585e19524b017f3035d940465c5c927d6";
$db_name = "VanitySales_donkeysang";
$db_port = "3305";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$result = null;

$current_month_total = 0;
$selected_period_total = 0;

$stmt = null;
$stmt_total = null;

$selected_year = isset($_POST['year']) ? $_POST['year'] : '';
$selected_month = isset($_POST['month']) ? $_POST['month'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $year = $_POST['year'];
    $month = $_POST['month'];

    if (!empty($year) && empty($month)) {
        // Fetch deleted expenses for the specified year
        $sql = "SELECT id, description, amount, date FROM expenses WHERE YEAR(date) = ? AND deleted = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $year);

        // Calculate total expense for the selected year
        $sql_total = "SELECT SUM(amount) as total FROM expenses WHERE YEAR(date) = ? AND deleted = 1";
        $stmt_total = $conn->prepare($sql_total);
        $stmt_total->bind_param("s", $year);


    } elseif (!empty($year) && !empty($month)) {
        // Fetch deleted expenses for the specified year and month
        $sql = "SELECT id, description, amount, date FROM expenses WHERE YEAR(date) = ? AND MONTH(date) = ? AND deleted = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $year, $month);

        // Calculate total expense for the selected year and month
        $sql_total = "SELECT SUM(amount) as total FROM expenses WHERE YEAR(date) = ? AND MONTH(date) = ? AND deleted = 1";
        $stmt_total = $conn->prepare($sql_total);
        $stmt_total->bind_param("ss", $year, $month);

        
    } elseif (empty($year) && !empty($month)) {
        // Fetch deleted expenses for the specified month of the current year
        $current_year = date('Y');
        $sql = "SELECT id, description, amount, date FROM expenses WHERE YEAR(date) = ? AND MONTH(date) = ? AND deleted = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $current_year, $month);

        // Calculate total expense for the selected month of the current year
        $sql_total = "SELECT SUM(amount) as total FROM expenses WHERE YEAR(date) = ? AND MONTH(date) = ? AND deleted = 1";
        $stmt_total = $conn->prepare($sql_total);
        $stmt_total->bind_param("ss", $current_year, $month);
    }

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }

    if ($stmt_total) {
        $stmt_total->execute();
        $total_result = $stmt_total->get_result();
        if ($total_row = $total_result->fetch_assoc()) {
            $selected_period_total = $total_row['total'];
        }
        $stmt_total->close();
    }
}

// Calculate total expense for the current month
$current_year = date('Y');
$current_month = date('m');
$sql_current_month_total = "SELECT SUM(amount) as total FROM expenses WHERE YEAR(date) = ? AND MONTH(date) = ? AND deleted = 1";
$stmt_current_month_total = $conn->prepare($sql_current_month_total);
$stmt_current_month_total->bind_param("ss", $current_year, $current_month);
$stmt_current_month_total->execute();
$current_month_result = $stmt_current_month_total->get_result();
if ($current_month_row = $current_month_result->fetch_assoc()) {
    $current_month_total = $current_month_row['total'];
}
$stmt_current_month_total->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="expenses.css">
    <style>

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

    <div class="container">
        <div class="table-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <select name="year" class="year-dropdown">
                        <option value="">Select Year</option>
                        <?php for ($i = 2024; $i <= 2034; $i++) : ?>
                            <option value="<?php echo $i; ?>" <?php echo ($selected_year == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="month" class="month-dropdown">
                        <option value="">Select Month</option>
                        <?php
                        $months = [
                            '01' => 'January',
                            '02' => 'February',
                            '03' => 'March',
                            '04' => 'April',
                            '05' => 'May',
                            '06' => 'June',
                            '07' => 'July',
                            '08' => 'August',
                            '09' => 'September',
                            '10' => 'October',
                            '11' => 'November',
                            '12' => 'December'
                        ];
                        foreach ($months as $num => $name) : ?>
                            <option value="<?php echo $num; ?>" <?php echo ($selected_month == $num) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Search" class="search-button">
                </div>
            </form>

            <?php if ($selected_year || $selected_month) : ?>
                <div class="total-container">
                    <div class="total-expense-container">
                        <div>Total Expense: <span>₱<?php echo number_format($selected_period_total, 2); ?></span></div>
                    </div>
                    <button class="add-expenses-button" onclick="window.location.href='add_expenses.php'">Add Expenses</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Expense Description</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result) {
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['description'] . "</td>";
                                    echo "<td>₱" . number_format($row['amount'], 2) . "</td>";
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td>
                                    <a href='edit_expense.php?id=" . $row["id"] . "' class='edit-btn'>Edit</a>
                                    <a href='delete_expense.php?id=" . $row["id"] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this item?\")'>Delete</a>
                                  </td>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No expenses found</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>Please select a month or year to view.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>