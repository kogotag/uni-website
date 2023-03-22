<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$tid = htmlspecialchars(trim(filter_input(INPUT_POST, 'tid')));
$p = htmlspecialchars(trim(filter_input(INPUT_POST, 'p')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    $errors = true;
    exit();
}

if ($tid == null || strlen($tid) === 0 || strlen($tid) > 255) {
    echo "Неправильный формат номера форума";
    exit();
}

if (!is_numeric($tid)) {
    echo "Номер форума не является числом";
    exit();
}

if ($p == null || strlen($p) === 0 || strlen($p) > 255) {
    echo "Неправильный формат номера страницы";
    exit();
}

if (!is_numeric($p)) {
    echo "Номер страницы не является числом";
    exit();
}

if (empty($p)) {
    $p = 1;
}

echo json_encode(forumGetPosts($tid, $p));
