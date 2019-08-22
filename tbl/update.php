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

$res = $pdo->prepare("SELECT * FROM calculate WHERE table_name=? AND db_name=?");
$res->execute(array($data['tbl'], $data['db']));
$formulas = $res->fetchAll();

$sql_update = "UPDATE ".$data['db'].".".$data['tbl']." SET ";
$sql_insert = "INSERT INTO ".$data['db'].".".$data['tbl']." (";
$new =[];
foreach ($data['rows'] as $key => $row) {
	if(!$row['id']) {
		$new[] = $row['cols'];
		continue;
	}
	$update = $sql_update;
	$update_fields = [];
	$update_values = [];
	foreach($row['cols'] as $field => $value) {
		//$changes[] = '`'.$field."`='".$value."'";
		$update_fields[] = '`'.$field.'`=?';
		$update_values[] = $value;
	}
	$update .= implode(', ', $update_fields);
	$update .= " WHERE `".$data['pk']."`=?";
	$update_values[] = $row['id'];
	$update_st = $pdo->prepare($update);
	$update_st->execute($update_values);
}
if(!empty($new)) {
	$inserts = [];
	$new_fields = array_keys($new[0]);
	$insert_values = [];
	foreach ($new_fields as $key => $value) {
		$new_fields[$key] = '`'.$value.'`';
	}
	$sql_insert .= implode(', ', $new_fields).") VALUES ";
	foreach ($new as $key => $new_values) {
		foreach ($new_values as $key => $value) {
			$insert_values[] = $value;
		}
		$inserts[] = "(".str_repeat('?, ', count($new_values)-1)."?)";
	}
	$sql_insert .= implode(', ', $inserts);
	
	$insert_st = $pdo->prepare($sql_insert);
	$insert_st->execute($insert_values);
}
foreach ($formulas as $key => $formula) {
	$calc = "UPDATE ".$formula['db_name'].".".$formula['table_name']." SET `".$formula['col_name']."`=".$formula['formula'];
	$pdo->query($calc);
}
$update = $pdo->prepare("UPDATE tables SET last_update=NOW(), last_user=? WHERE table_name=? AND db_name=?");
$update->execute(array($user['user_name'], $data['tbl'], $data['db']));
?>