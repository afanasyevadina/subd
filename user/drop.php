<?php
require_once('../connect.php');

$sql = "DROP USER '".$_GET['user']."'@'localhost'";
if(!$pdo->query($sql)) {
	$error = $pdo->errorInfo()[2];
	require_once('../error.php');
	exit;
}
$delete = $pdo->prepare("DELETE FROM users WHERE user_name=?");
$delete->execute(array($_GET['user']));
$delete_grants = $pdo->prepare("DELETE FROM grants WHERE user_name=?");
$delete_grants->execute(array($_GET['user']));
header('Location: /users.php');
?>