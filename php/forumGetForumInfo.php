<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$fid = htmlspecialchars(trim(filter_input(INPUT_POST, 'fid')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    exit();
}

if ($fid == null || strlen($fid) === 0 || strlen($fid) > 255) {
    echo "Неправильный формат номера форума";
    exit();
}

if (!is_numeric($fid)) {
    echo "Номер форума не является числом";
    exit();
}

$info = forumGetForumInfo($fid);
$info["topics_count"] = forumGetForumTopicsNumber($fid);

echo json_encode($info);
