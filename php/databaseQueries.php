<?php

$dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);

function getScheduleRowByCoordinates($semester, $week, $day, $class_number) {
    global $dbh;

    $stmt_schedule = $dbh->prepare("SELECT * FROM `ssau_schedule` WHERE semester=? AND week=? AND day=? AND class_number=?;");
    $exec_schedule = $stmt_schedule->execute(array($semester, $week, $day, $class_number));

    if (!$exec_schedule) {
        return [];
    }

    return $stmt_schedule->fetch();
}

function getScheduleRowBySubjectIdAndNumber($subject_id, $number) {
    global $dbh;

    $stmt_schedule = $dbh->prepare("SELECT * FROM `ssau_schedule` WHERE subject_id=? AND number=?;");
    $exec_schedule = $stmt_schedule->execute(array($subject_id, $number));

    if (!$exec_schedule) {
        return [];
    }

    return $stmt_schedule->fetch();
}

function scheduleGetAudios($semester, $week, $day, $class_number) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $stmt_day = $dbh->prepare("SELECT * FROM `subjects_audios` WHERE schedule_id=? ORDER BY `id`;");
    $exec_day = $stmt_day->execute(array($result_schedule["id"]));

    if (!$exec_day) {
        return [];
    }

    return $stmt_day->fetchAll();
}

function scheduleGetVideos($semester, $week, $day, $class_number) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $stmt_day = $dbh->prepare("SELECT * FROM `subjects_videos` WHERE schedule_id=? ORDER BY `id`;");
    $exec_day = $stmt_day->execute(array($result_schedule["id"]));

    if (!$exec_day) {
        return [];
    }

    return $stmt_day->fetchAll();
}

function scheduleGetComments($semester, $week, $day, $class_number) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $stmt_day = $dbh->prepare("SELECT * FROM `subjects_comments` WHERE schedule_id=? ORDER BY `id`;");
    $exec_day = $stmt_day->execute(array($result_schedule["id"]));

    if (!$exec_day) {
        return [];
    }

    return $stmt_day->fetchAll();
}

function scheduleGetDesc($semester, $week, $day, $class_number) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $stmt_get_current_desc = $dbh->prepare("SELECT * FROM `subjects_info` WHERE subject_id=? AND number=?;");
    $exec_get_current_desc = $stmt_get_current_desc->execute(array($result_schedule["subject_id"], $result_schedule["number"]));

    if (!$exec_get_current_desc) {
        return [];
    }

    return $stmt_get_current_desc->fetch();
}

function scheduleGetPrevDesc($semester, $week, $day, $class_number) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $result2_schedule = getScheduleRowBySubjectIdAndNumber($result_schedule["subject_id"], intval($result_schedule["number"]) - 1);

    $stmt_get_current_desc = $dbh->prepare("SELECT * FROM `subjects_info` WHERE subject_id=? AND number=?;");
    $exec_get_current_desc = $stmt_get_current_desc->execute(array($result2_schedule["subject_id"], $result2_schedule["number"]));

    if (!$exec_get_current_desc) {
        return [];
    }

    return $stmt_get_current_desc->fetch();
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

function getScheduleSubjectWithClassNumberBySemesterAndWeek($semester, $week, $class_number) {
    global $dbh;

    $schedule_row = [];

    for ($i = 0; $i < 6; $i++) {
        $stmt_schedule_row = $dbh->prepare("SELECT * FROM `ssau_schedule` WHERE semester=? AND week=? AND class_number=? AND day=? ORDER BY `day`");
        $exec_schedule_row = $stmt_schedule_row->execute(array($semester, $week, $class_number, $i + 1));
        if ($exec_schedule_row) {
            $schedule_row[$i] = $stmt_schedule_row->fetch();
        } else {
            $schedule_row[$i] = [];
        }
    }

    return $schedule_row;
}

function getSubjectAliasNameBySubjectNameId($subject_name_id) {
    global $dbh;

    $stmt_alias = $dbh->prepare("SELECT `aliasName` FROM `subjects_aliases` WHERE id=? LIMIT 1;");
    $exec_alias = $stmt_alias->execute(array($subject_name_id));

    $result = "";

    if ($exec_alias) {
        $result = $stmt_alias->fetch()["aliasName"];
    }

    return $result;
}

function addDescChange($semester, $week, $day, $class_number, $user_id, $hw_old, $hw_new) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $stmt_info_change = $dbh->prepare("INSERT INTO `subjects_info_changes` (`subject_id`, `number`, `user_id`, `hwWas`, `hwNew`) VALUES (?, ?, ?, ?, ?);");
    $exec_info_change = $stmt_info_change->execute(array($result_schedule["subject_id"], $result_schedule["number"], $user_id, $hw_old, $hw_new));

    return $exec_info_change;
}

function updateDesc($semester, $week, $day, $class_number, $hw) {
    global $dbh;

    $current_desc = scheduleGetDesc($semester, $week, $day, $class_number);

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    if (empty($current_desc)) {
        $stmt_insert_desc = $dbh->prepare("INSERT INTO `subjects_info` (`subject_id`, `number`, `hw`) VALUES (?, ?, ?);");
        $exec_insert_desc = $stmt_insert_desc->execute(array($result_schedule["subject_id"], $result_schedule["number"], $hw));
    } else {
        $stmt_update_desc = $dbh->prepare("UPDATE `subjects_info` SET hw=? WHERE subject_id=? AND number=?;");
        $exec_update_desc = $stmt_update_desc->execute(array($hw, $result_schedule["subject_id"], $result_schedule["number"]));
    }

    if ($exec_insert_desc || $exec_update_desc) {
        return "success";
    } else {
        return "";
    }
}
