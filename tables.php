<?php
$title = 'Таблицы';
require_once('header.php');
if($user['role'] != 'admin') {
	$grant = $pdo->prepare("SELECT * FROM grants WHERE user_name=? AND db_name=?") ;
	$grant->execute(array($user['user_name'], $_GET['db']));
	if(!$can = $grant->fetch()) { ?>
		<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
		<?php exit;
	}
}
$res = $pdo->prepare("SELECT * FROM tables WHERE db_name = ?");
$res->execute(array($_GET['db']));
$tables = $res->fetchAll();
function dateFormat($time) {
	if(time() - $time < 86400) {
		if(date('d', $time) == date('d')) {
			return date('в H:i:s', $time);
		} else {
			return date('вчера в H:i:s', $time);
		}
	} else {
		if(date('Y', $time) == date('Y')) {
			return date('d.m в H:i:s', $time);
		} else {
			return date('d.m.Y в H:i:s', $time);
		}
	}
}
?>
<div class="container-fluid">
	<h3>Таблицы базы данных <?=$_GET['db']?></h3>
	<hr>
	<?php if($user['role'] == 'admin' || $can['create_table']) { ?>
		<div class="form-group">
			<a class="btn btn-outline-success" href="newtable.php?db=<?=$_GET['db']?>">Добавить таблицу</a>
		</div>
	<?php } ?>
	<table class="table table-striped">
		<thead>
			<thead>
				<tr>
					<th>Название</th>
					<th>Дата создания</th>
					<th>Создатель</th>
					<th>Дата последнего изменения</th>
					<th>Записей</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($tables as $key => $tbl) {
					$sql = "SELECT COUNT(*) FROM ".$_GET['db'].".".$tbl['table_name'];
					$count = $pdo->query($sql)->fetchColumn();
				?>
					<tr>
						<td><?=$tbl['table_name']?></td>
						<td><?=dateFormat(strtotime($tbl['created_at']))?></td>
						<td><?=$tbl['creator']?></td>
						<td><?=$tbl['last_update'] ? ($tbl['last_user'].' '.dateFormat(strtotime($tbl['last_update']))) : '-'?></td>
						<td><?=$count?></td>
						<td style="text-align: right;">
							<?php if($can['select_data'] || $user['role'] == 'admin') { ?>
								<a class="btn btn-light" href="/table.php?db=<?=$_GET['db']?>&tbl=<?=$tbl['table_name']?>" title="Данные">
									<i class="fas fa-table"></i>
								</a>
							<?php } if($can['update_data'] || $user['role'] == 'admin') { ?>
								<a class="btn btn-light" href="/table_edit.php?db=<?=$_GET['db']?>&tbl=<?=$tbl['table_name']?>" title="Редактировать">
									<i class="fas fa-edit"></i>
								</a>
							<?php } if($can['create_table'] || $user['role'] == 'admin') { ?>
								<a class="btn btn-light" href="/table_construct.php?db=<?=$_GET['db']?>&tbl=<?=$tbl['table_name']?>" title="Конструктор">
									<i class="fas fa-cog"></i>
								</a>
								<button class="btn btn-light" data-toggle="modal" data-target="#drop<?=$key?>" title="Удалить">
									<i class="fas fa-trash-alt"></i>
								</button>
							<?php } ?>
						</td>
					</tr>
					<div class="modal fade" id="drop<?=$key?>" tabindex="-1" role="dialog" aria-labelledby="dropLabel<?=$key?>" aria-hidden="true">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="dropLabel<?=$key?>">Подтверждение</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        <p>Вы действительно хотите удалить таблицу? Отменить это действие будет невозможно.</p>
					      </div>
					      <div class="modal-footer">
					        <a href="/tbl/drop.php?db=<?=$_GET['db']?>&tbl=<?=$tbl['table_name']?>" class="btn btn-primary">Да</a>
					        <button class="btn btn-light" data-dismiss="modal">Нет</button>
					      </div>
					    </form>
					    </div>
					  </div>
					</div>
				<?php } ?>
			</tbody>
		</thead>
	</table>
</i>
</main>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>