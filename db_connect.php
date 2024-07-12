<?php
// Database credentials
$db_host = "zhw.h.filess.io";
$db_user = "VanitySales_donkeysang";
$db_pass = "385dd18585e19524b017f3035d940465c5c927d6";
$db_name = "VanitySales_donkeysang";
$db_port = "3305";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
