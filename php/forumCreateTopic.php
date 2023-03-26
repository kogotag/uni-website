<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$fid = htmlspecialchars(trim(filter_input(INPUT_POST, 'fid')));
$fname = htmlspecialchars(trim(filter_input(INPUT_POST, 'fname')));
$content = htmlspecialchars(trim(filter_input(INPUT_POST, 'post_content')));
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

if ($fid == null || strlen($fid) === 0 || strlen($fid) > 255) {
    echo "Неправильный формат номера форума";
    exit();
}

$forum_info = forumGetForumInfo($fid);

if (!$forum_info) {
    echo "указанный форум не найден";
    exit();
}

if (!is_numeric($fid)) {
    echo "Номер форума не является числом";
    exit();
}

if ($fname == null || strlen($fname) === 0 || strlen($fname) > 255) {
    echo "Неправильный формат названия темы";
    exit();
}

if ($content == null || empty($content) || strlen($content) > FORUM_MAX_POST_SIZE) {
    echo 'Пустое или слишком большое сообщение';
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

$tid = forumCreateTopic($fid, $fname, $_SESSION["user_id"]);

if (!$tid) {
    echo "Не удалось создать тему";
    exit();
}

$pid = forumAddPost($content, $tid, $_SESSION["user_id"]);

if (!$pid) {
    echo "Тема создана, но не удалось отправить первый пост";
    exit();
}

forumRenewTopicUpdateTime($tid);

for ($i = 0; $i < count($images); $i++) {
    forumPostAddImage($_SESSION["user_id"], $pid, $i + 1, htmlspecialchars($images[$i]));
}

echo "success";
