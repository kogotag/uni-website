<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$tid = htmlspecialchars(trim(filter_input(INPUT_POST, 'tid')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    $errors = true;
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

echo json_encode(forumBreadCrumbTopicPage($tid));
