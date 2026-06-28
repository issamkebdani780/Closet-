<?php
// Enable error reporting to find out exactly why it's failing
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

$host = getenv('MYSQLHOST') ?: "sql303.infinityfree.com";
$user = getenv('MYSQLUSER') ?: "if0_40611001";
$pass = getenv('MYSQLPASSWORD') ?: "rS3HAXQQqxmE";
$dbname = getenv('MYSQLDATABASE') ?: "if0_40611001_closet";
$port = (int)(getenv('MYSQLPORT') ?: 3306);

try {
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
} catch (mysqli_sql_exception $e) {
    die("Database Connection Error: " . $e->getMessage() . "<br>Host: $host | Port: $port | DB: $dbname");
}
?>
