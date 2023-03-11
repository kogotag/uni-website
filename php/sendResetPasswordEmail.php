<?php

require_once 'utils.php';
require_once 'databaseQueries.php';

try {
    $csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
    $email = htmlspecialchars(trim(filter_input(INPUT_POST, 'email')));
    $ip = filter_input(INPUT_SERVER, "REMOTE_ADDR");

    $errors = false;

    if ($csrf_token == null || !validateToken($csrf_token)) {
        echo 'Ошибка безопасности: csrf-token not set';
        exit();
    }

    if (!checkResetPasswordEmailRequestsNotTooOften($ip)) {
        echo 'Запрашивать сброс пароля можно не чаще, чем 1 раз в минуту.';
        exit();
    }

    if (!checkResetPasswordEmailRequestsDaily($ip)) {
        echo 'Достигнуто максимальное дневное количество запросов на сброс пароля. Если письмо не пришло, попробуйте запросить новый сброс пароля на следующий день или обратитесь в поддержку.';
        exit();
    }

    if ($email == null || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Неправильный формат электронной почты';
        $errors = true;
    } else if (!checkdnsrr(substr($email, strpos($email, '@') + 1), 'MX')) {
        echo 'Указанное доменное имя в адресе почты не существует';
        $errors = true;
    }

    if ($errors) {
        exit();
    }

    $code = "";

    $user_id = getUserByEmail($email);

    if (!$user_id) {
        echo "Пользователь с указанным адресом почты не найден";
        exit();
    }

    $code = generateResetPasswordCode($user_id);

    if (!$code) {
        echo "Не удалось сгенерировать секретный код. Обратитесь к администратору.";
        exit();
    }

    addResetPasswordEmailRequest($ip, $user_id);

    $email_subject = "Запрос на сброс пароля";
    $email_text = "Вы можете сбросить ваш пароль по ссылке\nhttps://mehaniki05.ru/resetPassword.php?resetId="
            . $code
            . "\n\nЕсли вы не запрашивали сброс пароля, проигнорируйте это письмо.";

    if (SEND_EMAILS) {
        $email_result = sendEmail($email, $email_subject, $email_text);
    } else {
        echo "Посылаю письмо на " . $email . "\n" . $email_subject . "\n" . $email_text;
        exit();
    }

    if (!$email_result) {
        echo "Не удалось отправить письмо";
        exit();
    }

    echo("success");
} catch (Exception $ex) {
    echo $ex->getMessage();
    exit();
}