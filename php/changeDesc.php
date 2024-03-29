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
$hw_from = htmlspecialchars(filter_input(INPUT_POST, "hwFrom"));
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

if (strlen($hw_from) > 1000) {
    echo "Слишком длинная строка в поле домашнего задания с пары<br>";
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

function updateText($text) {
    $text = str_replace("\n", "<br>", $text);
    $text = str_replace("&lt;br&gt;", "<br>", $text);
    return $text;
}

$hw_from = updateText($hw_from);

try {
    $current_desc = scheduleGetDesc($semester, $week, $day, $number);

    $hw_from_old = "";

    if (!empty($current_desc)) {
        $hw_from_old = $current_desc["hw"];
    }

    addDescChange($semester, $week, $day, $number, $_SESSION["user_id"], $hw_from_old, $hw_from);

    echo updateDesc($semester, $week, $day, $number, $hw_from);
} catch (Exception $ex) {
    echo $ex->getMessage();
}