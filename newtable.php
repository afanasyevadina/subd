<?php
$title = 'Новая таблица';
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

$types = $pdo->query("SELECT * FROM types")->fetchAll();
?>
<div class="container-fluid">
	<h3>Новая таблица</h3>
	<hr>
	<form action="/tbl/create.php" method="post">
		<input type="hidden" name="db" value="<?=$_GET['db']?>">
		<div class="row form-group">
			<label class="label col-lg-2">Название</label>
			<input type="text" name="tbl_name" class="form-control col-lg-6 nospace" autocomplete="off" autofocus required placeholder="Название">
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
				<?php for($i = 0; $i < 4; $i++) { ?>
				<tr>
					<td><input type="text" name="col[<?=$i?>][name]" class="form-control name nospace" autocomplete="off"></td>
					<td>
						<select name="col[<?=$i?>][type]" class="form-control type">
							<?php foreach ($types as $key => $type) { ?>
								<option value="<?=$type['real_name']?>"><?=$type['type_name']?></option>
							<?php } ?>
						</select>
					</td>
					<td><label><input class="required" type="checkbox" name="col[<?=$i?>][required]" value="NOT NULL"></label></td>
					<td><label><input class="key" type="checkbox" name="col[<?=$i?>][key]"></label></td>
					<td><label><input class="ai" type="checkbox" name="col[<?=$i?>][ai]" value="AUTO_INCREMENT"></label></td>
					<td><label><input class="calculate" type="checkbox" name="col[<?=$i?>][formula]"></label>
					<input type="hidden" name="col[<?=$i?>][default]" class="form-control default_value" autocomplete="off"></td>
					<td class="default"></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<div class="form-group">
			<input type="submit" value="Сохранить" class="btn btn-outline-success">
		</div>
	</form>
</div>
</main>

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
    </form>
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
</body>
</html>