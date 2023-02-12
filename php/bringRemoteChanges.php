<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

if (!isLoggedIn()) {
    echo 'Войдите, чтобы воспользоваться этой функцией';
    exit();
}

if ($_SESSION["user_admin_rank"] < 1) {
    echo 'Эта функция доступна только администраторам';
    exit();
}

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    exit();
}

try {
    
    echo "success";
} catch (Exception $ex) {
    echo $ex->getMessage();
}