<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

if (!isLoggedIn()) {
    echo 'Войдите, чтобы воспользоваться этой функцией';
    exit();
}

$errors = false;

$subject_id = htmlspecialchars(filter_input(INPUT_POST, "subject_id"));
$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    $errors = true;
    exit();
}

if ($subject_id == null || strlen($subject_id) === 0 || strlen($subject_id) > 255) {
    echo "Неправильный формат номера предмета<br>";
    $errors = true;
}

if ($errors) {
    exit;
}

if (!is_numeric($subject_id)) {
    echo "Номер предмета не число<br>";
    $errors = true;
}

if ($errors) {
    exit;
}

try {
    $result = getSubjectTypeBySubjectId($subject_id);
    
    if (empty($result)) {
        echo "По данному id не найден предмет";
        exit();
    }
    
    echo json_encode($result);
} catch (Exception $ex) {
    echo $ex->getMessage();
}