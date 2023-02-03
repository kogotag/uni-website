<?php

require_once 'utils.php';

try {
    $user_id = $_SESSION["user_id"];
    $cookie_remember = filter_input(INPUT_COOKIE, "remember");
    
    //TODO: may be check if cookie is empty separately? this may invoke error (?)
    if (empty($user_id) && !empty($cookie_remember)) {
        list($selector, $authenticator) = explode(':', $cookie_remember);
        
        //TODO: move to databaseQueries.php
        $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);

        $stmt_auth = $dbh->prepare("SELECT * FROM `authorization` WHERE `selector`=?;");
        $exec_auth = $stmt_auth->execute(array($selector));

        if (!$exec_auth) {
            exit();
        }

        $result_auth = $stmt_auth->fetch();

        if (empty($result_auth)) {
            exit();
        }

        $stmt_user = $dbh->prepare("SELECT * FROM `users` WHERE `id`=?;");
        $exec_user = $stmt_user->execute(array($result_auth["user_id"]));

        if (!$exec_user) {
            exit();
        }

        $user = $stmt_user->fetch();

        if (hash_equals($result_auth["token"], hash("sha256", base64_decode($authenticator)))) {
            $_SESSION["user_id"] = $result_auth["user_id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_login"] = $user["login"];
            $_SESSION["user_verified"] = $user["email_verified"];
            $_SESSION["user_from_group"] = intval($user["is_from_group"]);
            $_SESSION["user_admin_rank"] = intval($user["admin_rank"]);
            $_SESSION["user_auth_selector"] = $selector;
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

function isLoggedIn() {
    return !empty($_SESSION["user_id"]);
}
