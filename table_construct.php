<?php
$title = 'Таблица "'.$_GET['tbl'].'"';
require_once('header.php'); 

if($user['role'] != 'admin') {
	$grant = $pdo->prepare("SELECT * FROM grants WHERE user_name=? AND db_name=?") ;
	$grant->execute(array($user['user_name'], $_GET['db']));
	$can = $grant->fetch();
	if(!$can || !$can['create_table']) { ?>
		<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
		<?php exit;
	}
}
$types = $pdo->query("SELECT * FROM types ORDER BY ordered")->fetchAll();
$describe = "DESCRIBE ".$_GET['db'].'.'.$_GET['tbl'];
if(!$tbl = $pdo->query($describe)) { ?>
	<h4 style="padding: 30px">Данная таблица не существует.</h4>
<?php exit;
}
$table = $tbl->fetchAll();
$res = $pdo->prepare("SELECT * FROM calculate WHERE table_name=? AND db_name=?");
$res->execute(array($_GET['tbl'], $_GET['db']));
$formulas = $res->fetchAll();
$formulas = array_combine(array_column($formulas, 'col_name'), $formulas);
?>
<div class="container">
	<main class="slider">
		<div class="container-fluid" id="edit">
			<div class="row">
				<h3 class="col-lg-9">Конструктор "<?=$_GET['tbl']?>"</h3>
				
				<div class="dropdown col-lg-3">
				  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Конструктор
				  <span class="caret"></span></button>
				  <div class="dropdown-menu">
				    <?php if($can['select_data'] || $user['role'] == 'admin') { ?>
				    	<a class="dropdown-item" href="table.php?tbl=<?=$_GET['tbl']?>&db=<?=$_GET['db']?>">Просмотр</a> <?php } ?>
				    <?php if($can['update_data'] || $user['role'] == 'admin') { ?>
				    	<a class="dropdown-item" href="table_edit.php?tbl=<?=$_GET['tbl']?>&db=<?=$_GET['db']?>">Редактирование</a> <?php } ?>
				    <?php if($can['create_table'] || $user['role'] == 'admin') { ?>
				    	<a class="dropdown-item" href="table_construct.php?tbl=<?=$_GET['tbl']?>&db=<?=$_GET['db']?>">Конструктор</a> <?php } ?>
				  </div>
				</div>
			</div>
			<hr>
			<form action="/tbl/alter.php" method="post">
				<input type="hidden" name="db" value="<?=$_GET['db']?>">
				<div class="row form-group">
					<label class="label col-lg-2">Название</label>
					<input type="hidden" name="old_name" value="<?=$_GET['tbl']?>">
					<input type="text" name="tbl_name" class="form-control col-lg-6 nospace" autocomplete="off" required autofocus placeholder="Название" value="<?=$_GET['tbl']?>">
					<div class="col-lg-4" style="text-align: right;">
						<button type="button" class="btn btn-link pull-right" id="addfield">+ Добавить поле</button>
					</div>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th>Имя поля</th>
							<th>Тип данных</th>
							<th>Обязательное поле</th>
							<th>Ключевое поле</th>
							<th>Счётчик</th>
							<th>Вычисляемое поле</th>
							<th>По умолчанию</th>
							<th></th>
						</tr>
					</thead>
					<tbody class="construct">
						<?php foreach($table as $i => $col) { ?>
						<tr>
							<td>
								<input class="old" type="hidden" name="col[<?$i?>][old]" value="<?=$col['Field']?>">
								<input type="text" name="col[<?=$i?>][name]" class="form-control name nospace" autocomplete="off" value="<?=$col['Field']?>">
							</td>
							<td style="display: flex;">
								<select name="col[<?=$i?>][type]" class="form-control type">
									<?php foreach ($types as $key => $type) { ?>
										<option value="<?=strpos($col['Type'], $type['real_name']) !== false ? $col['Type'] : $type['real_name']?>" 
											<?=strpos($col['Type'], $type['real_name']) !== false ? 'selected' : ''?>>
											<?=$type['type_name']?>										
										</option>
									<?php } ?>
								</select>
							</td>
							<td><label>
								<input class="required" type="checkbox" name="col[<?=$i?>][required]" value="NOT NULL" <?=$col['Null'] == 'NO' ? 'checked' : ''?>>
							</label></td>
							<td><label>
								<input class="key" type="checkbox" name="col[<?=$i?>][key]" <?=$col['Key'] == 'PRI' ? 'checked' : ''?>>
							</label></td>
							<td><label>
								<input class="ai" type="checkbox" name="col[<?=$i?>][ai]" value="AUTO_INCREMENT" <?=$col['Extra'] == 'auto_increment' ? 'checked' : ''?>>
							</label></td>
							<td><label>
								<input class="calculate" type="checkbox" value="<?=@$formulas[$col['Field']]['formula']?>" 
								name="col[<?=$i?>][formula]" 
								<?=@$formulas[$col['Field']]['formula'] ? 'checked' : ''?>>
							</label></td>
							<input type="hidden" name="col[<?=$i?>][default]" class="form-control default_value" autocomplete="off" value="<?=$col['Default']?>">
							<td class="default"><?=$col['Default']?></td>
							<td><button type="button" class="btn btn-light drop"><i class="fas fa-trash-alt"></i></button></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<div class="form-group">
					<input type="submit" value="Сохранить" class="btn btn-outline-success">
					<?php if($can['drop_table'] || $user['role'] == 'admin') { ?>
						<button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#drop">Удалить</button>
					<?php } ?>
				</div>
			</form>
		</div>
	</main>
</div>
</main>

<div class="modal fade" id="drop" tabindex="-1" role="dialog" aria-labelledby="dropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dropLabel">Подтверждение</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Вы действительно хотите удалить таблицу? Отменить это действие будет невозможно.</p>
      </div>
      <div class="modal-footer">
        <a href="/tbl/drop.php?db=<?=$_GET['db']?>&tbl=<?=$_GET['tbl']?>" class="btn btn-outline-primary">Да</a>
        <button class="btn btn-light" data-dismiss="modal">Нет</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="enum" tabindex="-1" role="dialog" aria-labelledby="enumLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="enumLabel">Список значений</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" placeholder="Введите значения списка по одному на строку"></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-success" data-dismiss="modal" id="saveEnum">Готово</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="calculate" tabindex="-1" role="dialog" aria-labelledby="calculateLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="calculateLabel">Вычисляемое поле</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Значение поля равно:</p>
        <textarea id="formula" class="form-control" placeholder="Например, ИмяСтолбца1*2+ИмяСтолбца2"></textarea>
        <p class="error text-danger">Некорректная формула!</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-success" id="saveFormula">Готово</button>
      </div>
    </form>
    </div>
  </div>
</div>

<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/table.js"></script>
<script src="js/walking.js"></script>
</body>
</html>