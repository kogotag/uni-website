<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$content = htmlspecialchars(trim(filter_input(INPUT_POST, 'content')));
$tid = htmlspecialchars(trim(filter_input(INPUT_POST, 'tid')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    exit();
}

if (!isLoggedIn()) {
    echo "Для отправки сообщений войдите в учётную запись";
    exit();
}

if (intval($_SESSION["user_verified"]) < 1) {
    echo "Для отправки сообщений необходимо подтвердить адрес электронной почты";
    exit();
}

if ($content == null || empty($content) || strlen($content) > FORUM_MAX_POST_SIZE) {
    echo 'Пустое или слишком большое сообщение';
    exit();
}

if ($tid == null || strlen($tid) === 0 || strlen($tid) > 255) {
    echo "Неправильный формат номера темы";
    exit();
}

if (!is_numeric($tid)) {
    echo "Номер темы не является числом";
    exit();
}

forumAddPost($content, $tid, $_SESSION["user_id"]);

echo "success";