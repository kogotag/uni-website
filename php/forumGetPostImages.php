<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$pid = htmlspecialchars(trim(filter_input(INPUT_POST, 'pid')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    $errors = true;
    exit();
}

if ($pid == null || strlen($pid) === 0 || strlen($pid) > 255) {
    echo "Неправильный формат номера поста";
    exit();
}

if (!is_numeric($pid)) {
    echo "Номер поста не является числом";
    exit();
}

echo json_encode(forumGetPostImages($pid));