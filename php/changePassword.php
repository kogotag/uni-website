<?php

require_once 'auth.php';
require_once 'utils.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$passwordFirst = htmlspecialchars(trim(filter_input(INPUT_POST, 'passwordFirst')));
$passwordSecond = htmlspecialchars(trim(filter_input(INPUT_POST, 'passwordSecond')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo 'Ошибка безопасности: csrf-token not set';
    exit();
}

if (!isLoggedIn()) {
    echo 'Войдите, чтобы воспользоваться этой функцией';
    exit();
}

$user_id = $_SESSION["user_id"];

if ($passwordFirst == null || strlen($passwordFirst) > 255 || strlen($passwordFirst) < 8) {
    echo "Пароль должен содержать от 8 до 255 символов";
    exit();
}

if (is_null($passwordSecond) || $passwordSecond !== $passwordFirst) {
    echo "Пароли не совпадают";
    exit();
}

$result = changeUserPassword($user_id, password_hash($passwordFirst, PASSWORD_DEFAULT));

if ($result) {
    echo "success";
}