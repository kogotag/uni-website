<?php

require_once 'utils.php';
require_once 'databaseQueries.php';

function parseCheckbox($checkboxVal) {
    if (!empty($checkboxVal)) {
        return true;
    } else {
        return false;
    }
}

try {
    $csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
    $email = htmlspecialchars(trim(filter_input(INPUT_POST, 'email')));
    $checkResetPassword = htmlspecialchars(trim(filter_input(INPUT_POST, 'checkResetPassword'))); // not empty <=> checked

    $errors = false;

    if ($csrf_token == null || !validateToken($csrf_token)) {
        echo '<p>Ошибка безопасности: csrf-token not set</p>';
        exit();
    }

    if ($email == null || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<p>Неправильный формат электронной почты</p>';
        $errors = true;
    } else if (!checkdnsrr(substr($email, strpos($email, '@') + 1), 'MX')) {
        echo '<p>Указанное доменное имя в адресе почты не существует</p>';
        $errors = true;
    }

    if ($errors) {
        exit();
    }

    $checkResetPassword = parseCheckbox($checkResetPassword);
    $code = "";

    if ($checkResetPassword) {

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
    }
    
    $email_subject = "Проверка почтового сервера";
    $email_text = "Это письмо отправлено для проверки работы почтового сервера. Если вы его получили, сообщите администратору в личном сообщении.";
    if (!empty($code)) {
        $email_text .= "\n\nВы также запросили восстановление пароля. Для этого перейдите по ссылке:\nhttps://mehaniki05.ru/resetPassword.php?resetId=" . $code;
    }
    //echo "Посылаю письмо на " . $email . "\n" . $email_subject . "\n" . $email_text;
    $email_result = sendEmail($email, $email_subject, $email_text);
    
    if (!$email_result) {
        echo "Не удалось отправить письмо";
        exit();
    }
    
    echo("success");
    
} catch (Exception $ex) {
    echo $ex->getMessage();
    exit();
}