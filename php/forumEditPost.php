<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$content = htmlspecialchars(trim(filter_input(INPUT_POST, 'content')));
$pid = htmlspecialchars(trim(filter_input(INPUT_POST, 'pid')));

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

$result = forumEditPost($content, $pid);

if (!$result) {
    echo "Отправка не удалась. Обратитесь в поддержку";
    exit();
}

echo "success";
