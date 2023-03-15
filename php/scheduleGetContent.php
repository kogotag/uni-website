<?php

require_once 'config.inc.php';
require_once 'russianDateFormatter.php';
require_once 'databaseQueries.php';

function ensureNumericFieldFromInput($field) {
    if ($field == null || strlen($field) === 0 || strlen($field) > 255) {
        return 1;
    }

    if (!is_numeric($field)) {
        return 2;
    }

    return 0;
}

function invalidFormatNumericFieldErrorMessage($name, $ensure) {
    if ($ensure === 1) {
        echo $name, ' invalid format<br>';
        return false;
    } elseif ($ensure === 2) {
        echo $name, ' not number<br>';
        return false;
    } else {
        return true;
    }
}

$params_errors = false;

$semester = htmlspecialchars(filter_input(INPUT_POST, "semester"));
$week = htmlspecialchars(filter_input(INPUT_POST, "week"));
$day = htmlspecialchars(filter_input(INPUT_POST, "day"));
$number = htmlspecialchars(filter_input(INPUT_POST, "number"));

$params_errors |= !invalidFormatNumericFieldErrorMessage("semester", ensureNumericFieldFromInput($semester));
$params_errors |= !invalidFormatNumericFieldErrorMessage("week", ensureNumericFieldFromInput($week));
$params_errors |= !invalidFormatNumericFieldErrorMessage("day", ensureNumericFieldFromInput($day));
$params_errors |= !invalidFormatNumericFieldErrorMessage("number", ensureNumericFieldFromInput($number));

if ($params_errors) {
    exit();
}

function getAudios() {
    global $semester;
    global $week;
    global $day;
    global $number;

    $result = "";

    $audios = scheduleGetAudios($semester, $week, $day, $number);

    foreach ($audios as $audio) {
        $user_name = getUserById($audio["user_id"]);
        // тут нет проверки на существование юзера
        $split = explode("/", $audio["url"]);
        $audio_name = $split[count($split) - 1];
        $result .= '<span class="font-weight-bold">' . $user_name . '</span> добавил аудио <span class="font-weight-bold">' . $audio_name . '</span>:<br><audio controls><source src="' . $audio["url"] . '" type="audio/mpeg">Your browser does not support audio players</audio><br>';
    }

    return $result;
}

function getVideos() {
    global $semester;
    global $week;
    global $day;
    global $number;

    $result = "";

    $videos = scheduleGetVideos($semester, $week, $day, $number);

    foreach ($videos as $video) {
        $user_name = getUserById($video["user_id"]);
        $result .= '<span class="font-weight-bold">' . $user_name . '</span> добавил видео:<br><a href="' . $video["url"] . '">' . $video["url"] . '</a><br>';
    }

    return $result;
}

function getComments() {
    global $semester;
    global $week;
    global $day;
    global $number;

    $result = "";

    $comments = scheduleGetComments($semester, $week, $day, $number);

    foreach ($comments as $comment) {
        $user_name = getUserById($comment["user_id"]);
        $timestamp = new DateTime($comment["timestamp"]);
        $result .= '<small class="font-weight-bold">' . $user_name . '</small>&nbsp;<small class=text-muted>' . getTimeElapsed($timestamp) . '</small><p>' . $comment["content"] . '</p>';
    }

    return $result;
}

function getDesc() {
    global $semester;
    global $week;
    global $day;
    global $number;

    $desc = scheduleGetDesc($semester, $week, $day, $number);
    $desc_prev = scheduleGetPrevDesc($semester, $week, $day, $number);

    $hwOn = "";
    $hwFrom = "";

    if ($desc) {
        $hwFrom = $desc["hw"];
    }

    if ($desc_prev) {
        $hwOn = $desc_prev["hw"];
    }

    if (empty(trim($hwOn)) && empty(trim($hwFrom))) {
        return "";
    }

    return "<h5>Домашнее задание на эту пару</h5><p>" . $hwOn . "</p><h5>Домашнее задание, которое задали на этой паре</h5><p>" . $hwFrom . "</p>";
}

function getAttachments() {
    global $semester;
    global $week;
    global $day;
    global $number;

    $result = "";

    $attachments = scheduleGetAttachments($semester, $week, $day, $number);

    foreach ($attachments as $attachment) {
        $user_name = getUserById($attachment["user_id"]);
        $split = explode("/", $attachment["url"]);

        if (count($split) <= 0) {
            return "Ошибка названия файла";
        }

        $file_name = $split[count($split) - 1];

        $result .= '<span class="font-weight-bold">' . $user_name . '</span> добавил файл: <a href="' . $attachment["url"] . '">' . $file_name . '</a><br>';
    }

    return $result;
}

$response = [];

$response["audios"] = getAudios();
$response["videos"] = getVideos();
$response["comments"] = getComments();
$response["desc"] = getDesc();
$response["attachments"] = getAttachments();

echo json_encode($response);
