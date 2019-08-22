<?php
require_once('../connect.php');

if($user['role'] != 'admin') {
	$grant = $pdo->prepare("SELECT * FROM grants WHERE user_name=? AND db_name=?") ;
	$grant->execute(array($user['user_name'], $_POST['db']));
	$can = $grant->fetch();
	if(!$can || !$can['create_table']) { ?>
		<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
		<?php exit;
	}
}

$sql = "CREATE TABLE IF NOT EXISTS ".$_POST['db'].".".$_POST['tbl_name']." ( ";
$formulas = [];
$keys = [];
foreach ($_POST['col'] as $key => $col) {
	if($col['name']) {
		if($col['formula']) $formulas[] = "('".$_POST['db']."','".$_POST['tbl_name']."','".$col['name']."','".$col['formula']."')";
		
		$col['name'] = "`".$col['name']."`";
		$default = $col['default'] ? "DEFAULT '".$col['default']."'" : '';
		$sql .= $col['name'].' '.$col['type'].' '.$col['required'].' '.$col['ai'].' '.$default.', ';
		if($col['key']) $keys[] = $col['name'];		
	}
}
$sql .= "PRIMARY KEY (".implode(', ', $keys).")) ENGINE=INNODB";
if($pdo->query($sql)) {
	$insert = $pdo->prepare("INSERT INTO tables (db_name, table_name, creator, created_at) VALUES (?,?,?, NOW())");
	$insert->execute(array($_POST['db'], $_POST['tbl_name'], $user['user_name']));

	if(count($formulas)) {
		$calc = "INSERT INTO calculate (db_name, table_name, col_name, formula) VALUES ".implode(',', $formulas);
		$pdo->query($calc);
	}

	header('Location: /table_edit.php?db='.$_POST['db'].'&tbl='.$_POST['tbl_name']);
} else {
	$error = $pdo->errorInfo()[2];
	require_once('../error.php');
}
?>