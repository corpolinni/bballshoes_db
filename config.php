<?php
// ==============================
// DATABASE CONFIGURATION
// ==============================

// Database credentials
$db_server   = "localhost";      // usually localhost
$db_username = "root";           // your MySQL username
$db_password = "";               // your MySQL password (empty by default in XAMPP)
$db_name     = "sneak_algo";     // your database name

// Create connection
$db = new mysqli($db_server, $db_username, $db_password, $db_name);

// Check connection
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

// Optional: set character encoding to support special symbols
$db->set_charset("utf8mb4");
?>
