<?php

require_once 'php/utils.php';
try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);

    $ip = filter_input(INPUT_SERVER, "REMOTE_ADDR");

    $stmt_get_verify_visits = $dbh->prepare("SELECT * FROM `verify_visits` WHERE `ip`=?;");
    $exec_get_verify_visits = $stmt_get_verify_visits->execute(array($ip));

    $get_verify_visits = $stmt_get_verify_visits->fetch();

    if (empty($get_verify_visits)) {
        $stmt_add_verify_visit = $dbh->prepare("INSERT INTO `verify_visits` (`ip`) VALUES(?);");
        $exec_add_verify_visit = $stmt_add_verify_visit->execute(array($ip));
    } else {
        $stmt_inc_visits = $dbh->prepare("UPDATE `verify_visits` SET count=count+1 WHERE `ip`=?;");
        $stmt_inc_visits->execute(array($ip));

        if ($get_verify_visits["count"] >= VERIFY_PAGE_MAX_VISITS_PER_HOUR) {
            exit();
        }
    }

    // TODO: sql-scheduler which truncates verify_visits table once per hour

    $verification_code = htmlspecialchars(filter_input(INPUT_GET, "code"));

    if ($verification_code == null || empty($verification_code)) {
        echo "Некорректный код подтверждения";
        exit();
    }

    $stmt_check_verification = $dbh->prepare("SELECT * FROM `verification_links` WHERE `code`=?;");
    $exec_check_verification = $stmt_check_verification->execute(array($verification_code));

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
