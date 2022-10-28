<?php

require_once 'utils.php';

try {
    $login = htmlspecialchars(trim(filter_input(INPUT_POST, 'login')));
    $password = htmlspecialchars(trim(filter_input(INPUT_POST, 'password')));
    $csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));
    $ip = filter_input(INPUT_SERVER, "REMOTE_ADDR");

    $errors = false;

    if ($csrf_token == null || !validateToken($csrf_token)) {
        echo '<p>Ошибка безопасности: csrf-token not set</p>';
        exit();
    }
    
    if ($_SESSION["user_id"]){
        echo "<p>Вы уже вошли</p>";
        exit();
    }

    if ($login == null || empty($login) || strlen($login) > 255) {
        echo '<p>Неверный логин</p>';
        $errors = true;
    }

    if ($password == null || empty($password) || strlen($password) > 255) {
        echo '<p>Неправильный формат пароля</p>';
        $errors = true;
    }

    if ($errors) {
        exit();
    }

    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    
    $stmt_check_login_attempts = $dbh->prepare("SELECT * FROM `login_attempts` WHERE `ip`=?;");
    $exec_check_login_attempts = $stmt_check_login_attempts->execute(array($ip));
    
    if (!$exec_check_login_attempts) {
        echo '<p>Ошибка подключения к БД</p>';
        exit();
    }
    
    $check_login_attempts = $stmt_check_login_attempts->fetchAll();
    $time_now = new DateTime();
    $attempts_count = 0;
    
    foreach ($check_login_attempts as $attempt){
        $diff = $time_now->diff(new DateTime($attempt["timestamp"]));
        if ($diff->h < 1){
            $attempts_count += 1;
        }
    }
    
    if ($attempts_count >= LOGIN_MAX_ATTEMPTS_PER_HOUR){
        echo '<p>Количество попыток входа за час исчерпано. Попробуйте позже</p>';
        exit();
    }
    
    $stmt_add_attempt = $dbh->prepare("INSERT INTO `login_attempts` (`ip`) VALUES(?);");
    $exec_add_attempt = $stmt_add_attempt->execute(array($ip));

    $stmt_check_user = $dbh->prepare("SELECT * FROM `users` WHERE `login`=?;");
    $exec_check_user = $stmt_check_user->execute(array($login));

    if (!$exec_check_user) {
        echo '<p>Ошибка подключения к БД</p>';
        exit();
    }

    $user = $stmt_check_user->fetch();

    if (empty($user)) {
        echo '<p>Пользователь не найден</p>';
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    if (password_verify($password, $user["password_hash"])) {
        $selector = base64_encode(random_bytes(9));
        $authenticator = random_bytes(33);

        setcookie(
                "remember",
                $selector . ':' . base64_encode($authenticator),
                [
                    'expires' => time() + 2 * 365 * 24 * 60 * 60,
                    'path' => '/',
                    'domain' => 'mehaniki05.ru',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict',
                ]
        );

        $stmt_login = $dbh->prepare("INSERT INTO `authorization` (`user_id`, `selector`, `token`) VALUES(?, ?, ?);");
        $exec_login = $stmt_login->execute(array($user["id"], $selector, hash("sha256", $authenticator)));

        echo "success";
    } else {
        echo '<p>Неверный пароль</p>';
        exit();
    }
} catch (Exception $e) {
    echo $e->getMessage();
}