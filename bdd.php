<?php
$dsn = 'mysql:dbname=pickupproject;host=localhost';
$user = 'root';
$password = '';

try
{
	$pdo = new PDO($dsn,$user,$password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
	echo "PDO Erreur : ".$e->getMessage();
	die();
}
?>