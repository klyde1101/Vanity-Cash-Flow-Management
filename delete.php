<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM vanity_sales_2q2024 WHERE id = ?";


    if ($stmt = mysqli_prepare($conn, $query)) {

        mysqli_stmt_bind_param($stmt, "i", $id);


        if (mysqli_stmt_execute($stmt)) {
            echo "Sale deleted successfully.";
        } else {
            echo "Error deleting sale: " . mysqli_stmt_error($stmt);
        }


        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}


mysqli_close($conn);


header('Location: view_sales.php');
exit();
