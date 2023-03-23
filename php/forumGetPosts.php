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
    echo "Неправильный формат номера темы";
    exit();
}

if (!is_numeric($tid)) {
    echo "Номер темы не является числом";
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

$posts = forumGetPosts($tid, $p);

foreach ($posts as &$post) {
    if (array_key_exists("user_id", $_SESSION) && intval($post["author"]) === intval($_SESSION["user_id"])) {
        $post["owned"] = true;
    } else {
        $post["owned"] = false;
    }
}

echo json_encode($posts);
