<?php
require_once('../connect.php');
$data = json_decode(file_get_contents("php://input"), true);

if($user['role'] != 'admin') {
	$grant = $pdo->prepare("SELECT * FROM grants WHERE user_name=? AND db_name=?") ;
	$grant->execute(array($user['user_name'], $data['db']));
	$can = $grant->fetch();
	if(!$can || !$can['update_data']) { ?>
		<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
		<?php exit;
	}
}

$sql = "DELETE FROM ".$data['db'].".".$data['tbl']." WHERE ".$data['pk']."='".$data['id']."'";
echo $sql;
$pdo->query($sql);
$update = $pdo->prepare("UPDATE tables SET last_update=NOW(), last_user=? WHERE table_name=? AND db_name=?");
$update->execute(array($user['user_name'], $data['tbl'], $data['db']));
?>