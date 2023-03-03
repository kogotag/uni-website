<?php

require_once 'utils.php';

$login = htmlspecialchars(trim(filter_input(INPUT_POST, 'login')));
$password = htmlspecialchars(trim(filter_input(INPUT_POST, 'password')));
$password_repeat = htmlspecialchars(trim(filter_input(INPUT_POST, 'password_repeat')));
$name = htmlspecialchars(trim(filter_input(INPUT_POST, 'name')));
$email = htmlspecialchars(trim(filter_input(INPUT_POST, 'email')));
$acceptRules = htmlspecialchars(trim(filter_input(INPUT_POST, 'acceptRulesCheckbox')));
$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
$ip = filter_input(INPUT_SERVER, "REMOTE_ADDR");

$errors = [];

if ($csrf_token == null || !validateToken($csrf_token)) {
    $errors[] = 10;
}

if ($login == null || strlen($login) > 255 || !preg_match('/^[a-zA-Z0-9]+$/', $login)) {
    $errors[] = 1;
}

if ($name == null || strlen($name) > 255 || !preg_match('/^[a-zA-Z\x{0400}-\x{04FF} ]+$/u', $name)) {
    $errors[] = 2;
}

//if ($password == null || strlen($password) > 255 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\~?!@#\$%\^&\*])(?=.{8,})/', $password)) {
if ($password == null || strlen($password) > 255 || strlen($password) < 8) {
    $errors[] = 3;
} else if ($password_repeat == null || $password_repeat !== $password) {
    $errors[] = 4;
}

if ($email == null || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 5;
} else if (!checkdnsrr(substr($email, strpos($email, '@') + 1), 'MX')) {
    $errors[] = 6;
}

if (empty($acceptRules)) {
    $errors[] = 14;
}

if (count($errors) !== 0) {
    echo json_encode($errors);
    exit();
}

//$password_salt = generateRandomString();
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);

    $stmt_check_login = $dbh->prepare("SELECT `id` FROM `users` WHERE `login`=?;");
    $check_login = $stmt_check_login->execute(array($login));

    if ($check_login == null || !$check_login) {
        $stmt_check_login = null;
        $errors[] = 7;
        echo json_encode($errors);
        exit();
    }

    if (count($stmt_check_login->fetchAll()) > 0) {
        $stmt_check_login = null;
        $errors[] = 8;
    }

    $stmt_check_email = $dbh->prepare("SELECT `id` FROM `users` WHERE `email`=?;");
    $check_email = $stmt_check_email->execute(array($email));

    if ($check_email == null || !$check_email) {
        $stmt_check_email = null;
        $errors[] = 7;
        echo json_encode($errors);
        exit();
    }

    if (count($stmt_check_email->fetchAll()) > 0) {
        $stmt_check_login = null;
        $errors[] = 9;
    }

    $stmt_check_ip = $dbh->prepare("SELECT * FROM `mail_verifications_attempts` WHERE `ip`=?;");
    $exec_check_ip = $stmt_check_email->execute(array($ip));

    if ($exec_check_ip == null || !$exec_check_ip) {
        $stmt_check_ip = null;
        $errors[] = 7;
        echo json_encode($errors);
        exit();
    }

    $attempts_count = 0;
    $time_now = new DateTime();

    foreach ($stmt_check_ip->fetchAll() as $attempt_data) {
        $timestamp = new DateTime($attempt_data["timestamp"]);
        $days_since_attempt = $time_now->diff($timestamp)->d;
        if ($days_since_attempt === 0) {
            $attempts_count += 1;
        }
    }

    if ($attempts_count > SMTP_VERIFICATIONS_PER_DAY) {
        $errors[] = 12;
        echo json_encode($errors);
        exit();
    }

    if (count($errors) !== 0) {
        echo json_encode($errors);
        exit();
    }

    $stmt_register_user = $dbh->prepare("INSERT INTO `users` (`login`, `password_hash`, `name`, `email`) VALUES(?, ?, ?, ?);");
    $exec_register_user = $stmt_register_user->execute(array($login, $password_hash, $name, $email));

    $stmt_check_user = $dbh->prepare("SELECT * FROM `users` WHERE `email`=?;");
    $exec_check_user = $stmt_check_user->execute(array($email));

    if (!$exec_check_user) {
        $errors[] = 13;
        echo json_encode($errors);
        exit();
    }

    $user_id = $stmt_check_user->fetch()["id"];

    $verification_code = generateRandomString(16);

    $stmt_verification = $dbh->prepare("INSERT INTO `verification_links` (`code`, `user_id`) VALUES(?, ?);");
    $exec_verification = $stmt_verification->execute(array($verification_code, $user_id));

    $stmt_write_attempt_ip = $dbh->prepare("INSERT INTO `mail_verifications_attempts` (`ip`) VALUES (?);");
    $exec_write_attempt_ip = $stmt_write_attempt_ip->execute(array($ip));

    $mail_headers[] = "MIME-Version: 1.0";
    $mail_headers[] = "From: " . SMTP_FROM;
    $mail_headers[] = "Content-type: text/plain; charset=utf-8";

    mb_internal_encoding("UTF-8");
    $encoded_subject = mb_encode_mimeheader("Подтвердите регистрацию на нашем сайте", 'UTF-8', 'B', "\r\n", strlen('Subject: '));

    $mail_message = "Здравствуйте, " . $name . "! Перейдите по ссылке, чтобы подтвердить регистрацию: "
            . "https://mehaniki05.ru/verify.php?code=" . $verification_code;

    $result_of_email = mail($email, $encoded_subject, $mail_message, implode("\r\n", $mail_headers));
    if (!$result_of_email) {
        $errors[] = 11;
        echo(json_encode($errors));
        exit;
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br>";
    die();
}

if (count($errors) === 0) {
    $errors[] = 0;
    echo json_encode($errors);
}