<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// DATABASE CONNECTION INFO
$db_host = "zhw.h.filess.io";
$db_user = "VanitySales_donkeysang";
$db_pass = "385dd18585e19524b017f3035d940465c5c927d6";
$db_name = "VanitySales_donkeysang";
$db_port = "3305";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sales = [];
$expenses = [];

$selectedYear = $_POST['year'] ?? date('Y'); // Get the selected year from the dropdown. Default to current year if not set.

for ($i = 1; $i <= 12; $i++) {
    $sales_query = "SELECT SUM(price) as total_sales FROM vanity_sales_2q2024 WHERE MONTH(date) = $i AND YEAR(date) = $selectedYear";
    $expenses_query = "SELECT SUM(amount) as total_expenses FROM expenses WHERE MONTH(date) = $i AND YEAR(date) = $selectedYear AND deleted = 1";

    $sales_result = $conn->query($sales_query);
    $expenses_result = $conn->query($expenses_query);

    $sales[$i] = $sales_result->fetch_assoc()['total_sales'] ?: 0;
    $expenses[$i] = $expenses_result->fetch_assoc()['total_expenses'] ?: 0;
}

// Close the connection after all queries
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Sales and Expenses</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="home.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #f0f0f0;
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

        .container {
            padding: 20px;
        }

        .card {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-container {
            width: 70%;
            height: 100%;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .chart-container {
                width: 100%;
            }
        }

        .form-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .form-group select,
        .form-group input[type="submit"] {
            padding: 10px;
            margin-right: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            height: 50px;
            box-sizing: border-box;
        }

        .form-group input[type="submit"] {
            background-color: #424242;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            flex: 0 0 auto;
            align-self: stretch;
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

    <!-- Year dropdown and search button -->
    <div class="form-group">
        <form method="post">
            <select name="year">
                <?php for ($year = date('Y'); $year >= 2020; $year--) : ?>
                    <option value="<?php echo $year; ?>" <?php echo $year == $selectedYear ? 'selected' : ''; ?>><?php echo $year; ?></option>
                <?php endfor; ?>
            </select>
            <input type="submit" value="Search">
        </form>
    </div>

    <div class="chart-container">

        <canvas id="myChart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [{
                    label: 'Sales',
                    data: <?php echo json_encode(array_values($sales)); ?>,
                    backgroundColor: '#7CB9E8', // Sales bar color
                }, {
                    label: 'Expenses',
                    data: <?php echo json_encode(array_values($expenses)); ?>,
                    backgroundColor: '#fd5c63', // Expenses bar color
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true, // Allow the chart to expand to container width
                aspectRatio: 2, // Aspect ratio for width and height of the chart
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10000,
                        ticks: {
                            stepSize: 1000,
                            color: 'black', // Y-axis tick color
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)' // Y-axis grid line color
                        }
                    },
                    x: {
                        ticks: {
                            color: 'black', // X-axis tick color
                        },
                        grid: {
                            display: true // Disable X-axis grid lines
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'black', // Legend text color

                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)', // Tooltip background color
                        displayColors: false, // Hide color boxes in tooltip
                        callbacks: {
                            label: function(tooltipItem) {
                                var label = tooltipItem.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += tooltipItem.formattedValue;
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>