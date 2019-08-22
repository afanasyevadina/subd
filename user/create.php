<?php
require_once('../connect.php');

$sql = "CREATE USER '".$_POST['name']."'@'localhost' IDENTIFIED BY '".$_POST['password']."'";
if(!$pdo->query($sql)) {
	$error = $pdo->errorInfo()[2];
	require_once('../error.php');
	exit;
}
$insert = $pdo->prepare("INSERT INTO users (user_name, password, hash) VALUES (?,?,?)");
$insert->execute(array($_POST['name'], $_POST['password'], md5($_POST['user'].time())));
$grant = "GRANT ALL PRIVILEGES ON dbmanager.* TO '".$_POST['user']."'@'localhost'";
if(!$pdo->query($grant)) {
	$error = $pdo->errorInfo()[2];
	require_once('../error.php');
	exit;
}
header('Location: /users.php');
?>