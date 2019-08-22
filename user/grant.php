<?php
require_once('../connect.php');

$databases = $pdo->query("SELECT * FROM dbs")->fetchAll();
foreach ($databases as $db) {
	$revoke = "REVOKE ALL ON ".$db['db_name'].".* FROM '".$_POST['user']."'@'localhost'";
	$pdo->query($revoke);
	$grants = isset($_POST[$db['db_name']]) ? $_POST[$db['db_name']] : 0;
	$select_data = $grants ? $grants['select_data'] : 0;
	$update_data = $grants ? $grants['update_data'] : 0;
	$create_table = $grants ? $grants['create_table'] : 0;
	$drop_table = $grants ? $grants['drop_table'] : 0;
	if($select_data) {
		$sql = "GRANT SELECT ON ".$db['db_name'].".* TO '".$_POST['user']."'@'localhost'";
		$pdo->query($sql);
	}
	if($update_data) {
		$sql = "GRANT INSERT ON ".$db['db_name'].".* TO '".$_POST['user']."'@'localhost'";
		$pdo->query($sql);
		$sql = "GRANT DELETE ON ".$db['db_name'].".* TO '".$_POST['user']."'@'localhost'";
		$pdo->query($sql);
		$sql = "GRANT UPDATE ON ".$db['db_name'].".* TO '".$_POST['user']."'@'localhost'";
		$pdo->query($sql);
	}
	if($create_table) {
		$sql = "GRANT CREATE ON ".$db['db_name'].".* TO '".$_POST['user']."'@'localhost'";
		$pdo->query($sql);
		$sql = "GRANT ALTER ON ".$db['db_name'].".* TO '".$_POST['user']."'@'localhost'";
		$pdo->query($sql);
	}
	if($drop_table) {
		$sql = "GRANT DROP ON ".$db['db_name'].".* TO '".$_POST['user']."'@'localhost'";
		$pdo->query($sql);
	}
	if($grants) {
		$insert = $pdo->prepare("INSERT INTO grants (user_name, db_name, select_data, update_data, create_table, drop_table) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE user_name=VALUES(user_name), db_name=VALUES(db_name), select_data=VALUES(select_data), update_data=VALUES(update_data), create_table=VALUES(create_table), drop_table=VALUES(drop_table)");
		$insert->execute(array($_POST['user'], $db['db_name'], $select_data, $update_data, $create_table, $drop_table));
	} else {
		$delete = $pdo->prepare("DELETE FROM grants WHERE user_name=? AND db_name=?");
		$delete->execute(array($_POST['user'], $db['db_name']));
	}
}

$pdo->query("FLUSH PRIVILEGES");
header('Location: /users.php');
?>