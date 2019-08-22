<?php
require_once('../connect.php');

//если пользователь не имеет прав менять таблицу, его выгоняем
if($user['role'] != 'admin') {
	$grant = $pdo->prepare("SELECT * FROM grants WHERE user_name=? AND db_name=?") ;
	$grant->execute(array($user['user_name'], $_POST['db']));
	$can = $grant->fetch();
	if(!$can || !$can['create_table']) { ?>
		<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
		<?php exit;
	}
}

//структура таблицы
$describe = "DESCRIBE ".$_POST['db'].'.'.$_POST['old_name'];
$table = $pdo->query($describe)->fetchAll();
$old_cols = array_column($table, 'Field');

$sql = "ALTER TABLE ".$_POST['db'].".".$_POST['old_name']." ";

$keys = [];   //первичные ключи
$formulas = []; //вычисляемые значения
$calc = [];

foreach ($_POST['col'] as $key => $col) {
	if($col['name']) { //если название заполнено
		if($col['formula']) { 
			$formulas[] = "('".$_POST['db']."','".$_POST['tbl_name']."','".$col['name']."','".$col['formula']."')"; //добавляем в список формул
			$calc[] = '`'.$col['name']."`=".$col['formula'];
		}
		$col['name'] = "`".$col['name']."`";
		$default = $col['default'] !== "" ? "DEFAULT '".$col['default']."'" : ''; //установлено ли значение по умолчанию
		$required = $col['required'] ? "NOT NULL" : "NULL"; //обязательное ли поле
		if($col['old']) { //это поле не новое
			$index = array_search($col['old'], $old_cols);
			if($index >= 0) unset($old_cols[$index]); //удаляем это имя из списка старых полей
			$col['old'] = "`".$col['old']."`";

			$sql .= "CHANGE ".$col['old'].' '.$col['name'].' '.$col['type'].' '.$required.' '.$col['ai'].' '.$default.', '; //меняем атрибуты поля
		} else {
			$sql .= "ADD ".$col['name'].' '.$col['type'].' '.$col['required'].' '.$col['ai'].' '.$default.', '; // новое - добавляем поле в таблицу
		}
		if($col['key']) $keys[] = $col['name']; //если это первичный ключ
	}
}
foreach ($old_cols as $key => $col) { //они уже не встретились, значит, удалены
	$sql .= "DROP `".$col.'`, ';
}
$sql .= "DROP PRIMARY KEY, ADD PRIMARY KEY (".implode(', ', $keys)."), "; //обновляем ключи
$sql .= "RENAME ".$_POST['db'].".".$_POST['tbl_name']; //переименовываем таблицу
if($pdo->query($sql)) {

	$update = $pdo->prepare("UPDATE tables SET table_name=?, last_update=NOW(), last_user=? WHERE table_name=? AND db_name=?"); //обновляем данные в нашей бд
	$update->execute(array($_POST['tbl_name'], $user['user_name'], $_POST['old_name'], $_POST['db']));
	
	$delete = $pdo->prepare("DELETE FROM calculate WHERE db_name=? AND table_name=?"); //удаляем формулы, что были ранее
	$delete->execute(array($_POST['db'], $_POST['old_name']));
	if(count($formulas)) {
		$sql = "INSERT INTO calculate (db_name, table_name, col_name, formula) VALUES ".implode(',', $formulas); //сохраняем информация, как нам рассчитывать
		$pdo->query($sql);
		$update = "UPDATE ".$_POST['db'].".".$_POST['old_name']." SET ".implode(',', $calc); //соответственно просчитываем поля
		$pdo->query($update);	
	}
	header('Location: /table_construct.php?db='.$_POST['db'].'&tbl='.$_POST['tbl_name']);
} else {
	$error = $pdo->errorInfo()[2];
	require_once('../error.php');
}
?>