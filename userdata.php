<?php
$databaseConfig = [
    "host" => "localhost",
    "username" => "root",
    "password" => "",
	"database" => "ntcpc",
	"pwdatabase" => "password",
];

$conn = new mysqli($databaseConfig["host"],
$databaseConfig["username"],
$databaseConfig["password"],
$databaseConfig["database"]);

$pw = new mysqli($databaseConfig["host"],
$databaseConfig["username"],
$databaseConfig["password"],
$databaseConfig["pwdatabase"]);

mysqli_query($conn,"set names utf8");
mysqli_query($pw,"set names utf8");
?>