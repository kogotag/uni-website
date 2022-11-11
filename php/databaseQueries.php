<?php

$dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);

function scheduleGetAudios($semester, $week, $day, $number) {
    global $dbh;
    $stmt_day = $dbh->prepare("SELECT * FROM `subjects_audios` WHERE semester=? AND week=? AND day=? AND number=? ORDER BY `id`;");
    $result_day = $stmt_day->execute(array($semester, $week, $day, $number));
    if ($result_day) {
        return $stmt_day->fetchAll();
    } else {
        return [];
    }
}

function scheduleGetVideos($semester, $week, $day, $number) {
    global $dbh;
    $stmt_day = $dbh->prepare("SELECT * FROM `subjects_videos` WHERE semester=? AND week=? AND day=? AND number=? ORDER BY `id`;");
    $result_day = $stmt_day->execute(array($semester, $week, $day, $number));
    if ($result_day) {
        return $stmt_day->fetchAll();
    } else {
        return [];
    }
}

function scheduleGetComments($semester, $week, $day, $number) {
    global $dbh;

    $stmt_day = $dbh->prepare("SELECT * FROM `subjects_comments` WHERE semester=? AND week=? AND day=? AND number=? ORDER BY `id`;");
    $result_day = $stmt_day->execute(array($semester, $week, $day, $number));
    if ($result_day) {
        return $stmt_day->fetchAll();
    } else {
        return [];
    }
}

function scheduleGetDesc($semester, $week, $day, $number) {
    global $dbh;

    $stmt_get_current_desc = $dbh->prepare("SELECT * FROM `subjects_info` WHERE semester=? AND week=? AND day=? AND number=?;");
    $exec_get_current_desc = $stmt_get_current_desc->execute(array($semester, $week, $day, $number));

    if ($exec_get_current_desc) {
        return $stmt_get_current_desc->fetch();
    } else {
        return null;
    }
}

function getUserById($id) {
    global $dbh;
    $stmt_user = $dbh->prepare("SELECT * FROM `users` WHERE id=?");
    $result_user = $stmt_user->execute(array($id));
    $user_name = "Not Found";
    if ($result_user) {
        $user_name = $stmt_user->fetch()["name"];
    }
    return $user_name;
}
