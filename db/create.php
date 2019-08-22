<?php
require_once('../connect.php');

if($user['role'] != 'admin') { ?>
	<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
	<?php exit;
}

$sql = "CREATE DATABASE IF NOT EXISTS ".$_POST['name'];
if($pdo->query($sql)) {
	$insert = $pdo->prepare("INSERT INTO `dbs` (`db_name`) VALUES (?)");
	$insert->execute(array($_POST['name']));
	header('Location: /databases.php');
} else {
	$error = $pdo->errorInfo()[2];
	require_once('../error.php');
}
?>