<?php

require_once 'utils.php';
require_once 'databaseQueries.php';

try {
    $csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
    $passwordFirst = htmlspecialchars(trim(filter_input(INPUT_POST, 'passwordFirst')));
    $passwordSecond = htmlspecialchars(trim(filter_input(INPUT_POST, 'passwordSecond')));
    $resetId = htmlspecialchars(trim(filter_input(INPUT_POST, 'resetId')));

    if ($csrf_token == null || !validateToken($csrf_token)) {
        echo '<p>Ошибка безопасности: csrf-token not set</p>';
        exit();
    }

    if ($passwordFirst == null || strlen($passwordFirst) > 255 || strlen($passwordFirst) < 8) {
        echo "Пароль должен содержать от 8 до 255 символов";
        exit();
    }

    if (is_null($passwordSecond) || $passwordSecond !== $passwordFirst) {
        echo "Пароли не совпадают";
        exit();
    }
    
    $user_id = getUserIdForResetPasswordByCode($resetId);
    
    if (!$user_id) {
        echo "Неверный код для восстановления пароля. Пожалуйста, перейдите по ссылке из письма на почте.";
        exit();
    }
    
    $result = changeUserPassword($user_id, password_hash($passwordFirst, PASSWORD_DEFAULT));
    
    if ($result) {
        echo "success";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
    exit();
}