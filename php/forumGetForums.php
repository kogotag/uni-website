<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    exit();
}

$forums = forumGetForums();

foreach($forums as &$forum) {
    $forum_topic_number = forumGetForumTopicsNumber($forum["id"]);
    $forum["topics_count"] = $forum_topic_number;
}

echo json_encode($forums);