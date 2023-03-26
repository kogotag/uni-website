<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$content = htmlspecialchars(trim(filter_input(INPUT_POST, 'content')));
$tid = htmlspecialchars(trim(filter_input(INPUT_POST, 'tid')));
$images_json = trim(filter_input(INPUT_POST, 'images'));

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

$topic_check = forumGetTopicInfo($tid);

if (!$topic_check) {
    echo "Указанная тема не существует";
    exit();
}

$images = [];

if (!empty($images_json)) {
    try {
        $images = json_decode($images_json);
    } catch (Exception $ex) {
        echo "Не удалось раскодировать информацию об изображениях. Обратитесь к администратору";
        exit();
    }
}

if (count($images) > FORUM_MAX_IMAGES_PER_POST) {
    echo "Количество вложений превысило максимальное: " . strval(FORUM_MAX_IMAGES_PER_POST);
    exit();
}

foreach ($images as $image) {
    if (!is_numeric($image)) {
        echo "В качестве ссылки на вложение передано не число";
        exit();
    }

    $info = forumUploadGetInfo(intval($image));

    if (!$info) {
        echo "Ссылка на одно из вложений не была найдена в базе данных сервера";
        exit();
    }
}

$pid = forumAddPost($content, $tid, $_SESSION["user_id"]);

if (!$pid) {
    echo "Ошибка. Не удалось создать пост";
    exit();
}

forumRenewTopicUpdateTime($tid);

for ($i = 0; $i < count($images); $i++) {
    forumPostAddImage($_SESSION["user_id"], $pid, $i + 1, htmlspecialchars($images[$i]));
}

echo "success";
