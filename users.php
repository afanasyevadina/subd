<?php
$title = 'Пользователи';
require_once('header.php');

if($user['role'] != 'admin') { ?>
	<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
	<?php exit;
}

$users = $pdo->query("SELECT * FROM `users` WHERE role is null")->fetchAll();
 ?>
<div class="container">
	<h3>Пользователи</h3>
	<hr>
	<div class="form-group">
		<button class="btn btn-outline-success" data-toggle="modal" data-target="#newUser">Добавить пользователя</button>
	</div>
	<table class="table">
		<thead>
			<thead>
				<tr>
					<th>Имя</th>
					<th>Пароль</th>
					<th>Последняя активность</th>
					<th>Привилегии</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($users as $key => $user) {
				$dbs = $pdo->prepare("SELECT * FROM grants WHERE user_name = ?");
				$dbs->execute(array($user['user_name']));
				$all =$dbs->fetchAll();
				$names = array_column($all, 'db_name');
				$grants = array_combine($names, $all);
				?>
					<tr>
						<td><?=$user['user_name']?></td>
						<td><?=$user['password']?></td>
						<td><?=$user['last_ping'] ? date('d.m.Y H:i:s', strtotime($user['last_ping'])) : '-'?></td>
						<td><?=implode(', ', $names)?></td>
						<td style="text-align: right;">
							<button class="btn btn-light" data-toggle="modal" data-target="#edit<?=$key?>"><i class="fas fa-edit"></i></button>
							<button class="btn btn-light" data-toggle="modal" data-target="#drop<?=$key?>"><i class="fas fa-trash-alt"></i></button>
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
					        <p>Вы действительно хотите удалить пользователя? Отменить это действие будет невозможно.</p>
					      </div>
					      <div class="modal-footer">
					        <a href="/user/drop.php?user=<?=$user['user_name']?>" class="btn btn-outline-primary">Да</a>
					        <button class="btn btn-light" data-dismiss="modal">Нет</button>
					      </div>
					    </form>
					    </div>
					  </div>
					</div>
					<div class="modal fade" id="edit<?=$key?>" tabindex="-1" role="dialog" aria-labelledby="editLabel<?=$key?>" aria-hidden="true">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <form action="/user/grant.php" method="post">
					      <div class="modal-header">
					        <h5 class="modal-title" id="editLabel<?=$key?>">Настроить привилегии</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        	<input type="hidden" name="user" value="<?=$user['user_name']?>">
					        	<?php foreach ($databases as $k => $db) { ?>
					        		<div class="form-group privileges">
					        			<h4><?=$db['db_name']?></h4>
					        			<label>
					        				<input type="checkbox" name="<?=$db['db_name']?>[select_data]" value="1" 
					        				<?=$grants[$db['db_name']]['select_data'] ? 'checked' : '' ?>>
					        				Чтение данных
					        			</label>
					        			<label>
					        				<input type="checkbox" name="<?=$db['db_name']?>[update_data]" value="1" 
					        				<?=$grants[$db['db_name']]['update_data'] ? 'checked' : '' ?>>
					        				Изменение данных
					        			</label>
					        			<label>
					        				<input type="checkbox" name="<?=$db['db_name']?>[create_table]" value="1" 
					        				<?=$grants[$db['db_name']]['create_table'] ? 'checked' : '' ?>>
					        				Создание таблиц
					        			</label>
					        			<label>
					        				<input type="checkbox" name="<?=$db['db_name']?>[drop_table]" value="1" 
					        				<?=$grants[$db['db_name']]['drop_table'] ? 'checked' : '' ?>>
					        				Удаление таблиц
					        			</label>
					        		</div>
					        	<?php } ?>
					      </div>
					      <div class="modal-footer">
					        <input type="submit" value="Сохранить" class="btn btn-outline-success">
					      </div>
					    </form>
					    </div>
					  </div>
					</div>
				<?php } ?>
			</tbody>
		</thead>
	</table>
</div>
<div class="modal fade" id="newUser" tabindex="-1" role="dialog" aria-labelledby="newUserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form action="/user/create.php" method="POST">
      <div class="modal-header">
        <h5 class="modal-title" id="newUserLabel">Новый пользователь</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div class="form-group">
        	<input type="text" name="name" autocomplete="off" class="form-control" placeholder="Имя">
        </div>
        <div class="form-group">
        	<input type="password" name="password" autocomplete="off" class="form-control" placeholder="Пароль">
        </div>
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-outline-success" value="Сохранить">
      </div>
    </form>
    </div>
  </div>
</div>
</main>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>