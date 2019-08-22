<?php
require_once('connect.php');
if($pdo === null) {
  header('Location: /login.php');
}
if($user['role'] == 'admin') {
  $databases = $pdo->query("SELECT * FROM dbs")->fetchAll();
} else {
  $res = $pdo->prepare("SELECT * FROM grants WHERE user_name=?");
  $res->execute(array($user['user_name']));
  $all = $res->fetchAll();
  $names = array_column($all, 'db_name');
  $databases = array_combine($names, $all);
}

?>
<!DOCTYPE html>
<html>
<head>
  <title><?=$title?></title>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="/css/custom.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#list" aria-controls="list" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="/">subСУБД</a>

    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
      <li class="nav-item">
        <a class="nav-link" href="databases.php">Базы данных</a>
      </li>
      <?php if($user['role'] == 'admin') { ?>
          <li class="nav-item">
            <a class="nav-link" href="users.php">Пользователи</a>
          </li>
        <?php } ?>      
    </ul>
    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
      <li class="nav-item">        
        <span class="nav-link"><?=$user['user_name']?></span>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="login.php"><i class="fas fa-lock"></i>Выход</a>
      </li>
    </ul>
</nav>
<main class="bg-light">
  <div class="collapse show bg-dark" id="list">
    <ul class="list-group">
      <?php if($user['role'] == 'admin') { ?>
        <button class="btn btn-link list-group-item" data-toggle="modal" data-target="#new">+ Новая база данных</button>
      <?php } ?>
      <?php 
      $i = 0; 
      foreach($databases as $db) {
        $i++;
        $tables = $pdo->prepare("SELECT * FROM tables WHERE `db_name` = ?");
        $tables->execute(array($db['db_name'])); ?>
        <li class="list-group-item" data-toggle="collapse" data-target="#tables<?=$i?>">
          <label>
            <input type="checkbox" <?=isset($_GET['db']) && $_GET['db'] == $db['db_name'] ? 'checked' : '' ?>>
            <span>&gt;</span><?=$db['db_name']?>
          </label>
        </li>
        <ul class="collapse <?=isset($_GET['db']) && $_GET['db'] == $db['db_name'] ? 'show' : '' ?>" id="tables<?=$i?>">
          <?php if($user['role'] == 'admin' || $db['create_table']) { ?>
            <a href="newtable.php?db=<?=$db['db_name']?>" class="list-group-item">+ Новая таблица</a>
          <?php } ?>
          <?php while($tbl = $tables->fetch()) { ?>
            <a href="table.php?db=<?=$db['db_name']?>&tbl=<?=$tbl['table_name']?>" class="list-group-item table-item"><?=$tbl['table_name']?></a>
          <?php } ?>
        </ul>
      <?php } ?>
    </ul>
  </div>

<!-- Modal -->
<div class="modal fade" id="new" tabindex="-1" role="dialog" aria-labelledby="newLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form action="/db/create.php" method="POST">
      <div class="modal-header">
        <h5 class="modal-title" id="newLabel">Новая база данных</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="text" name="name" autocomplete="off" class="form-control" placeholder="Название">
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-outline-success" value="Сохранить">
      </div>
    </form>
    </div>
  </div>
</div>