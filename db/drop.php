<?php
require_once('../connect.php');

if($user['role'] != 'admin') { ?>
	<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
	<?php exit;
}

$sql = "DROP DATABASE ".$_GET['db'];
if($pdo->query($sql)) {
	$delete = $pdo->prepare("DELETE FROM `dbs` WHERE `db_name`=?");
	$delete->execute(array($_GET['db']));
	header('Location: /databases.php');
} else {
	$error = $pdo->errorInfo()[2];
	require_once('../error.php');
}
?>