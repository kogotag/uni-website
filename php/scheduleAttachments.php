<?php

require_once 'config.inc.php';

$errors = [];

$semester = htmlspecialchars(filter_input(INPUT_POST, "semester"));
$week = htmlspecialchars(filter_input(INPUT_POST, "week"));
$day = htmlspecialchars(filter_input(INPUT_POST, "day"));
$number = htmlspecialchars(filter_input(INPUT_POST, "number"));

if ($semester == null || strlen($semester) === 0 || strlen($semester) > 255){
    $errors[] = 1;
}

if ($week == null || strlen($week) === 0 || strlen($week) > 255){
    $errors[] = 2;
}

if ($day == null || strlen($day) === 0 || strlen($day) > 255){
    $errors[] = 3;
}

if ($number == null || strlen($number) === 0 || strlen($number) > 255){
    $errors[] = 4;
}

if (!is_numeric($semester)){
    $errors[] = 5;
}

if (!is_numeric($week)){
    $errors[] = 6;
}

if (!is_numeric($day)){
    $errors[] = 7;
}

if (!is_numeric($number)){
    $errors[] = 8;
}

if (count($errors) !== 0){
    echo json_encode($errors);
    exit;
}

