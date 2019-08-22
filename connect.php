<?php
$pdo=new PDO('mysql:host=localhost;dbname=dbmanager;charset=utf8', 'root', '');
$pdo->query('set names utf8 collate utf8_unicode_ci;');
$pdo->query("USE dbmanager");

if(isset($_COOKIE['db_user'])) {
	$res = $pdo->prepare("SELECT * FROM `users` WHERE hash=?");
	$res->execute(array($_COOKIE['db_user']));
	if($user = $res->fetch()) {
		$pdo=new PDO('mysql:host=localhost;dbname=dbmanager;charset=utf8', $user['user_name'], $user['password']);
		$lp = $pdo->prepare("UPDATE users SET last_ping=NOW() WHERE user_name=?");
		$lp->execute(array($user['user_name']));
	} else {
		$pdo = null;
	}
} else {
	$pdo = null;
}
//$pdo=new PDO('sqlsrv:host=localhost\SQLEXPRESS;Database=plans');
?>