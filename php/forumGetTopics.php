<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$fid = htmlspecialchars(trim(filter_input(INPUT_POST, 'fid')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    $errors = true;
    exit();
}

if ($fid == null || strlen($fid) === 0 || strlen($fid) > 255) {
    echo "Неправильный формат семестра";
    exit();
}

if (!is_numeric($fid)) {
    echo "Номер форума не является числом";
    exit();
}

$topics = forumGetTopics($fid);

foreach ($topics as &$topic) {
    $topic_posts_info = forumGetTopicPostsNumber($topic["id"]);
    $topic["posts_count"] = $topic_posts_info["posts_count"];
    $topic["pages_count"] = $topic_posts_info["pages_count"];
}

echo json_encode($topics);
