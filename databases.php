<?php
$title = 'Базы данных';
require_once('header.php'); ?>
<div class="container">
	<h3>Базы данных</h3>
	<hr>
    <?php if($user['role'] == 'admin') { ?>
		<div class="form-group">
			<button class="btn btn-outline-success" data-toggle="modal" data-target="#new">Добавить базу данных</button>
		</div>
	<?php } ?>
	<table class="table table-striped">
		<thead>
			<thead>
				<tr>
					<th>Название</th>
					<th>Количество таблиц</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($databases as $key => $db) {
				$tables = $pdo->prepare("SELECT COUNT(*) FROM tables WHERE `db_name` = ?");
				$tables->execute(array($db['db_name']));
				?>
					<tr>
						<td><?=$db['db_name']?></td>
						<td><?=$tables->fetchColumn()?></td>
						<td style="text-align: right;">
							<a class="btn btn-light" href="/tables.php?db=<?=$db['db_name']?>">Список таблиц</a>
							<?php if($user['role'] == 'admin') { ?>
								<button class="btn btn-light" data-toggle="modal" data-target="#drop<?=$key?>"><i class="fas fa-trash-alt"></i></button>
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
					        <p>Вы действительно хотите удалить базу данных? Отменить это действие будет невозможно.</p>
					      </div>
					      <div class="modal-footer">
					        <a href="/db/drop.php?db=<?=$db['db_name']?>" class="btn btn-primary">Да</a>
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