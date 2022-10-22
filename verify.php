<?php

require_once 'php/utils.php';

$verification_code = htmlspecialchars(filter_input(INPUT_GET, "code"));

if ($verification_code == null || empty($verification_code)) {
    echo "Некорректный код подтверждения";
    exit();
}

try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    $stmt_check_verification = $dbh->prepare("SELECT * FROM `verification_links` WHERE `code`=?;");
    $exec_check_verification = $stmt_check_verification->execute(array($verification_code));

    if (!$exec_check_verification) {
        echo "Ошибка запроса БД";
        exit();
    }

    $check_verification_result = $stmt_check_verification->fetchAll();

    if (count($check_verification_result) !== 1) {
        echo "Код подтверждения недействителен";
        exit();
    }

    $link_id = $check_verification_result[0]["id"];
    $user_id = $check_verification_result[0]["user_id"];
    $used = $check_verification_result[0]["used"];

    if (intval($used) === 1) {
        echo "Вы уже активировали свой аккаунт ранее";
        exit();
    }

    $stmt_close_verification = $dbh->prepare("UPDATE `verification_links` SET `used`=1 WHERE `id`=?;");
    $exec_close_verification = $stmt_close_verification->execute(array($link_id));

    $stmt_verify_user = $dbh->prepare("UPDATE `users` SET `email_verified`=1 WHERE `id`=?;");
    $exec_verify_user = $stmt_verify_user->execute(array($user_id));

    if ($exec_close_verification && $exec_verify_user) {
        echo "Вы успешно зарегистрировались";
    }

    $dbh = null;
} catch (Exception $e) {
    echo $e->getMessage();
}