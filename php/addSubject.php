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
$subject_id = htmlspecialchars(filter_input(INPUT_POST, "subject_id"));
$lecturer = htmlspecialchars(filter_input(INPUT_POST, "lecturer"));
$room = htmlspecialchars(filter_input(INPUT_POST, "room"));
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

if ($subject_id == null || strlen($subject_id) === 0 || strlen($subject_id) > 255) {
    echo "Неправильный формат номера типа предмета<br>";
    $errors = true;
}

if ($lecturer == null || strlen($lecturer) === 0 || strlen($lecturer) > 2000) {
    echo "Неправильный формат текста в поле \"Преподаватель\"<br>";
    $errors = true;
}

if ($room == null || strlen($room) === 0 || strlen($room) > 2000) {
    echo "Неправильный формат текста в поле \"Аудитория\"<br>";
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

if (!is_numeric($subject_id)) {
    echo "Номер предмета не число<br>";
    $errors = true;
}

if ($errors) {
    exit;
}

try {
    // Проверить, нет ли по этим координатам предмета
    $check_result = checkIfCellFilled($semester, $week, $day, $number);
    if ($check_result) {
        echo "Эта ячейка уже занята";
        exit();
    }
    // Увеличить номер предметов, которые идут по расписанию после вставляемого
    $increment_result = incrementSubjectsNumberAfterCoordinate($semester, $week, $day, $number, $subject_id);
    
    if (!$increment_result) {
        echo "Не удалось увеличить номер следующих предметов";
        exit();
    }
    // Вставить предмет в расписание
    
    $insert_result = insertSubjectByCoordinatesAndType($semester, $week, $day, $number, $subject_id, $lecturer, $room);
    
    if (!$insert_result) {
        echo "Не удалось добавить предмет";
        exit();
    }
    
    echo "success";
} catch (Exception $ex) {
    echo $ex->getMessage();
}