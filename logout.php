<?php

require_once 'php/utils.php';
require_once 'php/auth.php';

if (!isLoggedIn()) {
    exit();
}

try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);

    $stmt_auth_logout = $dbh->prepare("DELETE FROM `authorization` WHERE `selector`=?;");
    $exec_auth_logout = $stmt_auth_logout->execute(array($_SESSION["user_auth_selector"]));
} catch (Exception $ex) {
    echo $ex->getMessage();
}

unset($_SESSION["user_id"]);
unset($_SESSION["user_name"]);
unset($_SESSION["user_email"]);
unset($_SESSION["user_login"]);
unset($_SESSION["user_verified"]);
unset($_SESSION["user_from_group"]);
unset($_SESSION["user_admin_rank"]);
unset($_SESSION["user_auth_selector"]);

setcookie(
        "remember",
        "",
        [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => 'mehaniki05.ru',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]
);

header("Location: /index.php");
