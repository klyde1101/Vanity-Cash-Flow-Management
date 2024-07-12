<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $current_time = date('Y-m-d H:i:s');

    $query = "UPDATE expenses SET deleted=0, date=? WHERE id = ?";

    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "si", $current_time, $id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Expense updated successfully.";
        } else {
            $_SESSION['message'] = "Error updating expense: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "Error preparing statement: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
header('Location: expenses.php'); // Redirect to expenses page
exit();
