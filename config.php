<?php
$host = getenv('MYSQLHOST') ?: "sql303.infinityfree.com";
$user = getenv('MYSQLUSER') ?: "if0_40611001";
$pass = getenv('MYSQLPASSWORD') ?: "rS3HAXQQqxmE";
$dbname = getenv('MYSQLDATABASE') ?: "if0_40611001_closet";
$port = getenv('MYSQLPORT') ?: 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}
?>
