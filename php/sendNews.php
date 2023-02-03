<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

if (!isLoggedIn()) {
    echo 'Войдите, чтобы отправить сообщение';
    exit();
}

if ($_SESSION["user_admin_rank"] < 1) {
    echo "Для этого действия требуются права администратора";
    exit();
}

$errors = false;

$content = htmlspecialchars(filter_input(INPUT_POST, "content"));
$heading = htmlspecialchars(filter_input(INPUT_POST, "heading"));
$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    $errors = true;
    exit();
}

if ($content == null || strlen($content) === 0 || strlen($content) > 2000) {
    echo "Неправильный формат сообщения<br>";
    $errors = true;
}

if ($heading == null || strlen($heading) === 0 || strlen($heading) > 2000) {
    echo "Неправильный формат сообщения в заголовке<br>";
    $errors = true;
}

if ($errors) {
    exit;
}

try {
    $result = sendNews($_SESSION["user_id"], replaceNewLineWithHtmlTag($content), $heading);
    
    if ($result) {
        echo "success";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}

