<?php
require_once 'config.inc.php';

$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_STRING);
$password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);

if (mb_strlen($login) < 4 || mb_strlen($login) > 90) {
    echo 'Логин слишком длинный или слишком короткий';
    exit();
}

if (mb_strlen($password) < 4 || mb_strlen($password) > 90) {
    echo 'Пароль слишком длинный или слишком короткий';
    exit();
}

$mysqli = new mysqli('localhost', 'root', 'root', 'uni_website');

$result = $mysqli->query("SELECT * FROM `users` WHERE `login` = '$login'");

$mysqli->close();

$user = $result->fetch_assoc();

if (count($user) == 0) {
    echo 'not found';
    exit();
}

print_r($user);