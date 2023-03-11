<?php

require_once 'utils.php';
require_once 'databaseQueries.php';

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
    
    $check_login_attempts = checkLoginAttempts($ip);
    
    if ($check_login_attempts === false) {
        echo '<p>Ошибка подключения к БД</p>';
        exit();
    }
    
    //TODO: переделать эту помойку по новому. см databaseQueries
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
    
    addLoginAttempt($ip, $login);

    $user = loginAttemptCheckUser($login);
    
    //тут тоже этот баг с !. Если юзер эмпти, то в первом ифе уже прервется
    if (!$user) {
        echo '<p>Ошибка подключения к БД</p>';
        exit();
    }

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
                    'domain' => DOMAIN_NAME,
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict',
                ]
        );

        loginSaveAuthorization($user["id"], $selector, hash("sha256", $authenticator));

        echo "success";
    } else {
        echo '<p>Неверный пароль</p>';
        exit();
    }
} catch (Exception $e) {
    echo $e->getMessage();
}