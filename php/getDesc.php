<?php

require_once 'config.inc.php';

$errors = [];

$semester = htmlspecialchars(filter_input(INPUT_POST, "semester"));
$week = htmlspecialchars(filter_input(INPUT_POST, "week"));
$day = htmlspecialchars(filter_input(INPUT_POST, "day"));
$number = htmlspecialchars(filter_input(INPUT_POST, "number"));

if ($semester == null || strlen($semester) === 0 || strlen($semester) > 255) {
    $errors[] = 1;
}

if ($week == null || strlen($week) === 0 || strlen($week) > 255) {
    $errors[] = 2;
}

if ($day == null || strlen($day) === 0 || strlen($day) > 255) {
    $errors[] = 3;
}

if ($number == null || strlen($number) === 0 || strlen($number) > 255) {
    $errors[] = 4;
}

if (!is_numeric($semester)) {
    $errors[] = 5;
}

if (!is_numeric($week)) {
    $errors[] = 6;
}

if (!is_numeric($day)) {
    $errors[] = 7;
}

if (!is_numeric($number)) {
    $errors[] = 8;
}

if (count($errors) !== 0) {
    echo json_encode($errors);
    exit;
}

try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    $stmt_get_current_desc = $dbh->prepare("SELECT * FROM `subjects_info` WHERE semester=? AND week=? AND day=? AND number=?;");
    $exec_get_current_desc = $stmt_get_current_desc->execute(array($semester, $week, $day, $number));

    if (!$exec_get_current_desc) {
        echo "Ошибка подключения к базе";
        exit();
    }

    $current_desc = $stmt_get_current_desc->fetch();

    $hwOn = $current_desc["hwOn"];
    $hwFrom = $current_desc["hwFrom"];

    if (empty(trim($hwOn)) && empty(trim($hwFrom))) {
        exit();
    }

    echo "<h5>Домашнее задание на эту пару</h5><p>" . $hwOn . "</p><h5>Домашнее задание, которое задали на этой паре</h5><p>" . $hwFrom . "</p>";
} catch (Exception $ex) {
    print($ex->getMessage());
}