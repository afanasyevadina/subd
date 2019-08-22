<?php
require_once('../connect.php');

if($user['role'] != 'admin') {
	$grant = $pdo->prepare("SELECT * FROM grants WHERE user_name=? AND db_name=?") ;
	$grant->execute(array($user['user_name'], $_GET['db']));
	$can = $grant->fetch();
	if(!$can || !$can['create_table']) { ?>
		<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
		<?php exit;
	}
}

$sql = "DROP TABLE ".$_GET['db'].".".$_GET['tbl'];
if($pdo->query($sql)) {
	$delete = $pdo->prepare("DELETE FROM tables WHERE table_name=? AND db_name=?");
	$delete->execute(array($_GET['tbl'], $_GET['db']));
	header('Location: /tables.php?db='.$_GET['db']);
} else {
	$error = $pdo->errorInfo()[2];
	require_once('../error.php');
}
?>