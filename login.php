<?php
setcookie('db_user', '', time()-3600);
if(!empty($_POST)) {
	$pdo=new PDO('mysql:host=localhost;dbname=dbmanager;charset=utf8', 'root', '');
	$pdo->query('set names utf8 collate utf8_unicode_ci;');
	$res = $pdo->prepare("SELECT * FROM users WHERE user_name=? AND password = ?");
	$res->execute(array($_POST['name'], $_POST['password']));
	if($user = $res->fetch()) {
		setcookie('db_user', $user['hash'], time()+3600*24*30);
		header('Location: /');
	} else {
		$message = "Неправильное имя пользователя или пароль!";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Вход</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/custom.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
</head>
<body>
<main class="bg-light login">
	<form action="" method="post">
		<h3>Авторизация</h3>
		<hr>
		<p class="text-danger" <?=$message ? '' :'hidden' ?>><?=$message?></p>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text" id="user-name"><i class="fas fa-user"></i></span>
				</div>
				<input type="text" name="name" class="form-control" autocomplete="off" autofocus placeholder="Имя пользователя" aria-label="Имя пользователя" aria-describedby="user-name">
			</div>
		</div>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text" id="user-pass"><i class="fas fa-key"></i></span>
				</div>
				<input type="password" name="password" class="form-control" placeholder="Пароль" aria-label="Пароль" aria-describedby="user-pass">
			</div>
		</div>
		<input type="submit" value="Войти" class="btn btn-success">
	</form>	
</main>
</body>
</html>