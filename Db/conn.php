<?php



$serverName = "localhost"; // Remove 'https://' and any protocol prefixes
$userName = "root";//u115172255_mcsUser 
$password = "";//c8M8UMp@&
$dbName = "excus";//u115172255_MCS



// $serverName = "localhost"; // Remove 'https://' and any protocol prefixes
// $userName = "u115172255_mcsUser";//u115172255_mcsUser 
// $password = "c8M8UMp@&";//c8M8UMp@&
// $dbName = "u115172255_MCS";//u115172255_MCS

try {
    $pdo = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection Failed: " . $e->getMessage();
    exit; // Stop execution if connection fails
}
