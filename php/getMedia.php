<?php

require_once 'config.inc.php';

$errors = [];

$semester = htmlspecialchars(filter_input(INPUT_POST, "semester"));
$week = htmlspecialchars(filter_input(INPUT_POST, "week"));
$day = htmlspecialchars(filter_input(INPUT_POST, "day"));
$number = htmlspecialchars(filter_input(INPUT_POST, "number"));

if ($semester == null || strlen($semester) === 0 || strlen($semester) > 255) {
    $errors[] = 1;
}

if ($week == null || strlen($week) === 0 || strlen($week) > 255) {
    $errors[] = 2;
}

if ($day == null || strlen($day) === 0 || strlen($day) > 255) {
    $errors[] = 3;
}

if ($number == null || strlen($number) === 0 || strlen($number) > 255) {
    $errors[] = 4;
}

if (!is_numeric($semester)) {
    $errors[] = 5;
}

if (!is_numeric($week)) {
    $errors[] = 6;
}

if (!is_numeric($day)) {
    $errors[] = 7;
}

if (!is_numeric($number)) {
    $errors[] = 8;
}

if (count($errors) !== 0) {
    echo json_encode($errors);
    exit;
}

try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    $stmt_day = $dbh->prepare("SELECT * FROM `subjects_videos` WHERE semester=? AND week=? AND day=? AND number=? ORDER BY `id`;");
    $result_day = $stmt_day->execute(array($semester, $week, $day, $number));
    $videos = [];
    if ($result_day) {
        $videos = $stmt_day->fetchAll();
    }

    foreach ($videos as $video) {
        $stmt_user = $dbh->prepare("SELECT * FROM `users` WHERE id=?");
        $result_user = $stmt_user->execute(array($video["user_id"]));
        $user_name = "Not Found";
        if ($result_user) {
            $user_name = $stmt_user->fetch()["name"];
        }
        echo '<span class="font-weight-bold">', $user_name, '</span> добавил видео:<br><a href="', $video["video_url"], '">', $video["video_url"], '</a><br>';
    }
} catch (Exception $ex) {
    print($ex->getMessage());
}