<?php

require_once 'utils.php';
require_once 'auth.php';

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
$hwOn = htmlspecialchars(filter_input(INPUT_POST, "hwOn"));
$hwFrom = htmlspecialchars(filter_input(INPUT_POST, "hwFrom"));
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

if (strlen($hwOn) > 1000) {
    echo "Слишком длинная строка в поле домашнего задания на пару<br>";
    $errors = true;
}

if (strlen($hwFrom) > 1000) {
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

try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);

    $stmt_get_current_desc = $dbh->prepare("SELECT * FROM `subjects_info` WHERE semester=? AND week=? AND day=? AND number=?;");
    $exec_get_current_desc = $stmt_get_current_desc->execute(array($semester, $week, $day, $number));

    $hwOn_old = "";
    $hwFrom_old = "";
    $current_desc = [];
    if ($exec_get_current_desc) {
        $current_desc = $stmt_get_current_desc->fetch();
        if (!empty($current_desc)) {
            $hwOn_old = $current_desc["hwOn"];
            $hwFrom_old = $current_desc["hwFrom"];
        }
    }

    $stmt_add_change = $dbh->prepare("INSERT INTO `subjects_info_changes` (`semester`, `week`, `day`, `number`, `user_id`, `hwOnWas`, `hwOn`, `hwFromWas`, `hwFrom`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $exec_add_change = $stmt_add_change->execute(array($semester, $week, $day, $number, $_SESSION["user_id"], $hwOn_old, $hwOn, $hwFrom_old, $hwFrom));

    if (empty($current_desc)) {
        $stmt_insert_desc = $dbh->prepare("INSERT INTO `subjects_info` (`semester`, `week`, `day`, `number`, `hwOn`, `hwFrom`) VALUES(?, ?, ?, ?, ?, ?);");
        $exec_insert_desc = $stmt_insert_desc->execute(array($semester, $week, $day, $number, $hwOn, $hwFrom));
    } else {
        $stmt_update_desc = $dbh->prepare("UPDATE `subjects_info` SET hwOn=?, hwFrom=? WHERE semester=? AND week=? AND day=? AND number=?;");
        $exec_update_desc = $stmt_update_desc->execute(array($hwOn, $hwFrom, $semester, $week, $day, $number));
    }

    if ($exec_insert_desc || $exec_update_desc) {
        echo "success";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}