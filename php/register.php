<?php

require_once 'config.inc.php';

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

$login = htmlspecialchars(trim(filter_input(INPUT_POST, 'login')));
$password = htmlspecialchars(trim(filter_input(INPUT_POST, 'password')));
$password_repeat = htmlspecialchars(trim(filter_input(INPUT_POST, 'password_repeat')));
$name = htmlspecialchars(trim(filter_input(INPUT_POST, 'name')));
$email = htmlspecialchars(trim(filter_input(INPUT_POST, 'email')));

$errors = [];

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

if (count($errors) !== 0) {
    echo(json_encode($errors));
    exit();
}

$password_salt = generateRandomString();
$password_hash = password_hash($password . $password_salt, PASSWORD_DEFAULT);

try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);

    $stmt_check_login = $dbh->prepare("SELECT `id` FROM `users` WHERE `login`=?");
    $check_login = $stmt_check_login->execute(array($login));

    if ($check_login == null || !$check_login) {
        $stmt_check_login = null;
        $errors[] = 7;
        exit();
    }

    if (count($stmt_check_login->fetchAll()) > 0) {
        $stmt_check_login = null;
        $errors[] = 8;
    }

    $stmt_check_email = $dbh->prepare("SELECT `id` FROM `users` WHERE `email`=?");
    $check_email = $stmt_check_email->execute(array($email));

    if ($check_email == null || !$check_email) {
        $stmt_check_email = null;
        $errors[] = 7;
        exit();
    }

    if (count($stmt_check_email->fetchAll()) > 0) {
        $stmt_check_login = null;
        $errors[] = 9;
    }

    if (count($errors) !== 0) {
        echo(json_encode($errors));
        exit();
    }

    $stmt_insert = $dbh->prepare("INSERT INTO `users` (`login`, `password_hash`, `password_salt`, `name`, `email`) VALUES(?, ?, ?, ?, ?);");
    if (!$stmt_insert->execute(array($login, $password_hash, $password_salt, $name, $email))) {
        $stmt_insert = null;
        $errors[] = 7;
        exit();
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