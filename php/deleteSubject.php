<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

if (!isLoggedIn()) {
    echo 'Войдите, чтобы воспользоваться этой функцией';
    exit();
}

if ($_SESSION["user_admin_rank"] < 1) {
    echo 'Эта функция доступна только администраторам';
    exit();
}

$errors = false;

$semester = htmlspecialchars(filter_input(INPUT_POST, "semester"));
$week = htmlspecialchars(filter_input(INPUT_POST, "week"));
$day = htmlspecialchars(filter_input(INPUT_POST, "day"));
$number = htmlspecialchars(filter_input(INPUT_POST, "number"));
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
    $row = getScheduleRowByCoordinates($semester, $week, $day, $number);
    
    if (empty($row)) {
        echo "Не удалось найти предмет в этой ячейке";
        exit();
    }
    
    $row_id = $row["id"];
    $subject_id = $row["subject_id"];
    $order_number = $row["number"];
    
    $modify_result = decreaseNumberOfNextSubjects($subject_id, $order_number);
    
    if (!$modify_result) {
        echo "Не удалось провести перенумерацию предметов в расписании";
        exit();
    }
    
    deleteAllAttachmentsFromScheduleCell($semester, $week, $day, $number);
    
    $delete_result = deleteSubjectByRowId($row_id);
    
    if(!$delete_result) {
        echo "Не удалось удалить предмет из расписания";
        exit();
    }
    
    echo "success";
} catch (Exception $ex) {
    echo $ex->getMessage();
}