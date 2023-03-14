<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

if (!isLoggedIn()) {
    echo 'Войдите, чтобы отправить сообщение';
    exit();
}

if ($_SESSION["user_from_group"] !== 1) {
    echo 'Только студенты нашей группы могут отправлять сообщения';
    exit();
}

if ($_SESSION["user_admin_rank"] !== 1) {
    echo 'Только администраторы могут добавлять аудио';
    exit();
}

$errors = false;

$semester = htmlspecialchars(filter_input(INPUT_POST, "semester"));
$week = htmlspecialchars(filter_input(INPUT_POST, "week"));
$day = htmlspecialchars(filter_input(INPUT_POST, "day"));
$number = htmlspecialchars(filter_input(INPUT_POST, "number"));
$content = $_FILES["content"];
$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    $errors = true;
    exit();
}

if ($semester == null || strlen($semester) === 0 || strlen($semester) > 255) {
    echo "Неправильный формат семестра<br>";
    $errors = true;
}

if ($week == null || strlen($week) === 0 || strlen($week) > 255) {
    echo "Неправильный формат недели<br>";
    $errors = true;
}

if ($day == null || strlen($day) === 0 || strlen($day) > 255) {
    echo "Неправильный формат дня<br>";
    $errors = true;
}

if ($number == null || strlen($number) === 0 || strlen($number) > 255) {
    echo "Неправильный формат номера предмета<br>";
    $errors = true;
}

if (empty($content)) {
    echo "Неправильный формат сообщения<br>";
    $errors = true;
}

if ($errors) {
    exit;
}

if (!is_numeric($semester)) {
    echo "Семестр не число<br>";
    $errors = true;
}

if (!is_numeric($week)) {
    echo "Неделя не число<br>";
    $errors = true;
}

if (!is_numeric($day)) {
    echo "День не число<br>";
    $errors = true;
}

if (!is_numeric($number)) {
    echo "Номер предмета не число<br>";
    $errors = true;
}

if ($errors) {
    exit;
}

try {
    $schedule_row = getScheduleRowByCoordinates($semester, $week, $day, $number);
    $subject_id = $schedule_row["subject_id"];
    
    $upload_dir = WEB_SERVER_FOLDER . "/" . FILES_FOLDER_REFERENCE . "/semester" . $semester . "/" . $subject_id . "/";
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755);
    }
    
    $file_full_name = htmlspecialchars($content["name"]);

    if (str_contains($file_full_name, "/")) {
        echo "Имя файла не должно содержать символа &#47;";
        exit();
    }

    if ($content["size"] > MAX_AUDIO_FILE_SIZE) {
        echo "Размер файла не должен превышать " . MAX_AUDIO_FILE_SIZE . " байт";
        exit();
    }

    $file_name_parts = explode(".", $file_full_name);
    $file_name = "";
    $file_extension = "";
    if (count($file_name_parts) === 1) {
        $file_name = $file_name_parts[0];
        $file_extension = "";
    } else {
        $file_name = implode(array_slice($file_name_parts, 0, count($file_name_parts) - 1));
        $file_extension = $file_name_parts[count($file_name_parts) - 1];
    }

    if (strtolower($file_extension) !== "mp3") {
        echo "Разрешённые расширения файла: mp3";
        exit();
    }

    $target_file = $upload_dir . $file_full_name;
    $i = 1;
    while (true) {
        if (!file_exists($target_file)) {
            break;
        }
        $target_file = $upload_dir . $file_name . $i . "." . ($file_extension ? $file_extension : "");
        $i++;
    }

    if (!move_uploaded_file($content["tmp_name"], $target_file)) {
        echo "Ошибка: файл не был загружен";
        exit();
    }

    $db_file_name = str_replace("/var/www/html", "", $target_file);
    
    $add_audio = addAudio($semester, $week, $day, $number, $_SESSION["user_id"], $db_file_name);
    
    if ($add_audio) {
        echo "success";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}