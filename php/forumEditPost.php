<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$content = htmlspecialchars(trim(filter_input(INPUT_POST, 'content')));
$pid = htmlspecialchars(trim(filter_input(INPUT_POST, 'pid')));
$images_json = trim(filter_input(INPUT_POST, 'images'));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    exit();
}

if (!isLoggedIn()) {
    echo "Для редактирования сообщений войдите в учётную запись";
    exit();
}

if (intval($_SESSION["user_verified"]) < 1) {
    echo "Для редактирования сообщений необходимо подтвердить адрес электронной почты";
    exit();
}

if ($content == null || empty($content) || strlen($content) > FORUM_MAX_POST_SIZE) {
    echo 'Пустое или слишком большое сообщение';
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

$post_info = forumGetPostInfo($pid);

if ($_SESSION["user_id"] !== $post_info["author"]) {
    echo "Вы не можете редактировать чужой пост";
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

$result = forumEditPost($content, $pid);

if (!$result) {
    echo "Отправка не удалась. Обратитесь в поддержку";
    exit();
}

forumRenewTopicUpdateTime($tid);

$delete = forumPostDeleteImages($pid);

if (!$delete) {
    echo "Не удалось изменить вложения поста";
    exit();
}

for ($i = 0; $i < count($images); $i++) {
    forumPostAddImage($_SESSION["user_id"], $pid, $i + 1, $images[$i]);
}

echo "success";
