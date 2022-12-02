<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

if (!isLoggedIn()) {
    echo 'Войдите, чтобы отправить сообщение';
    exit();
}

if ($_SESSION["user_from_group"] !== 1) {
    echo 'Только студенты нашей группы могут отправлять сообщения';
    exit();
}

$errors = false;

$semester = htmlspecialchars(filter_input(INPUT_POST, "semester"));
$week = htmlspecialchars(filter_input(INPUT_POST, "week"));
$day = htmlspecialchars(filter_input(INPUT_POST, "day"));
$number = htmlspecialchars(filter_input(INPUT_POST, "number"));
$content = htmlspecialchars(filter_input(INPUT_POST, "content"));
$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    $errors = true;
    exit();
}

if ($semester == null || strlen($semester) === 0 || strlen($semester) > 255) {
    echo "Неправильный формат семестра<br>";
    $errors = true;
}

if ($week == null || strlen($week) === 0 || strlen($week) > 255) {
    echo "Неправильный формат недели<br>";
    $errors = true;
}

if ($day == null || strlen($day) === 0 || strlen($day) > 255) {
    echo "Неправильный формат дня<br>";
    $errors = true;
}

if ($number == null || strlen($number) === 0 || strlen($number) > 255) {
    echo "Неправильный формат номера предмета<br>";
    $errors = true;
}

if ($content == null || strlen($content) === 0 || strlen($content) > 2000) {
    echo "Неправильный формат сообщения<br>";
    $errors = true;
}

if ($errors) {
    exit;
}

if (!is_numeric($semester)) {
    echo "Семестр не число<br>";
    $errors = true;
}

if (!is_numeric($week)) {
    echo "Неделя не число<br>";
    $errors = true;
}

if (!is_numeric($day)) {
    echo "День не число<br>";
    $errors = true;
}

if (!is_numeric($number)) {
    echo "Номер предмета не число<br>";
    $errors = true;
}

if ($errors) {
    exit;
}

try {
    $result = addComment($semester, $week, $day, $number, $user_id, $content);

    if ($result) {
        echo "success";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}