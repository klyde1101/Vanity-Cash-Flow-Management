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
$current_year_total = 0;

$stmt = null;
$stmt_total = null;

$selected_year = isset($_POST['year']) ? $_POST['year'] : '';
$selected_month = isset($_POST['month']) ? $_POST['month'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $year = $_POST['year'];
    $month = $_POST['month'];

    if (!empty($year) && empty($month)) {
        // Fetch sales for the specified year
        $sql = "SELECT * FROM vanity_sales_2q2024 WHERE YEAR(date) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $year);

        // Calculate total sales for the selected year
        $sql_total = "SELECT SUM(price) as total FROM vanity_sales_2q2024 WHERE YEAR(date) = ?";
        $stmt_total = $conn->prepare($sql_total);
        $stmt_total->bind_param("s", $year);

    } elseif (!empty($year) && !empty($month)) {
        // Fetch sales for the specified year and month
        $sql = "SELECT * FROM vanity_sales_2q2024 WHERE YEAR(date) = ? AND MONTH(date) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $year, $month);

        // Calculate total sales for the selected year and month
        $sql_total = "SELECT SUM(price) as total FROM vanity_sales_2q2024 WHERE YEAR(date) = ? AND MONTH(date) = ?";
        $stmt_total = $conn->prepare($sql_total);
        $stmt_total->bind_param("ss", $year, $month);
        
    } elseif (empty($year) && !empty($month)) {
        // Fetch sales for the specified month of the current year
        $current_year = date('Y');
        $sql = "SELECT * FROM vanity_sales_2q2024 WHERE YEAR(date) = ? AND MONTH(date) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $current_year, $month);

        // Calculate total sales for the selected month of the current year
        $sql_total = "SELECT SUM(price) as total FROM vanity_sales_2q2024 WHERE YEAR(date) = ? AND MONTH(date) = ?";
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
} else {
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VANITY SALES</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="ViewSales.css">
    <style>
        .total-sales-container {
            font-family: 'Roboto', sans-serif;
            font-weight: 300;
            font-size: 18px;
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: left;
            margin-left: 15%;
            display: inline-block;
            width: 60%;
        }

        .total-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .total-sales-container span {
            font-weight: 400;
        }

        .add-sales-button {
            background-color: #4682B4;
            border-radius: 4px;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            position: relative;
            margin-top: 10px;
            margin-bottom: 10px;
            margin-right: 15%;
            height: 40px;
            font-size: 12pt;
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

            <div class="total-container">
                <div class="total-sales-container">
                    <div>Total Sales: <span>₱<?php echo number_format($selected_period_total, 2); ?></span></div>
                </div>
                <button class="add-sales-button" onclick="window.location.href='add_sales.php'">Add Sales</button>
            </div>


            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>MOP</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Stock</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_price = 0;
                    $item_count = 0;
                    $bart_count = 0;
                    $butterfly_count = 0;
                    $paid_count = 0;
                    $unpaid_count = 0;

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $status_text = $row["status"] == 1 ? 'PAID' : 'UNPAID';
                            $status_class = $row["status"] == 1 ? 'status-paid' : 'status-unpaid';


                            $stock_class = '';
                            switch ($row['stock']) {
                                case 'In Stock':
                                    $stock_class = 'stock-in-stock';
                                    break;
                                case 'In Production':
                                    $stock_class = 'stock-in-production';
                                    break;
                                case 'No Stock':
                                    $stock_class = 'stock-no-stock';
                                    break;
                                default:
                                    $stock_class = '';
                                    break;
                            }

                            $color_class = '';

                            switch ($row['color']) {
                                case 'White':
                                    $color_class = 'color-white';
                                    break;
                                case 'Black':
                                    $color_class = 'color-black';
                                    break;
                                case 'Purple':
                                    $color_class = 'color-purple';
                                    break;
                                default:
                                    $color_class = '';
                                    break;
                            }

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['item']) . "</td>";
                            echo "<td><span class='color-label $color_class'>" . htmlspecialchars($row['color']) . "</span></td>";
                            echo "<td>" . htmlspecialchars($row['size']) . "</td>";
                            echo "<td>₱" . number_format($row['price'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($row['mop']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['channel']) . "</td>";
                            echo "<td><span class='" . $status_class . "'>" . $status_text . "</span></td>";
                            echo "<td><span class='" . $stock_class . "'>" . htmlspecialchars($row['stock']) . "</span></td>"; // Display stock info with class
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>
                    <a href='edit.php?id=" . $row['id'] . "' class='edit-btn'>Edit</a>
                    <a href='delete.php?id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this item?\")'>Delete</a>
                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No sales data found</td></tr>";
                        echo "Please select a year and month to view.";
                    }
                    ?>
                </tbody>
            </table>



</body>

</html>
<?php $conn->close(); ?>