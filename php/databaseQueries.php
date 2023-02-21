<?php

require_once 'utils.php';
require_once 'semesterTime.php';

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

function scheduleGetAttachments($semester, $week, $day, $class_number) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $stmt_day = $dbh->prepare("SELECT * FROM `subjects_attachments` WHERE schedule_id=? ORDER BY `id`;");
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

//TODO: REFACTOR THESE TWO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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

function getSubjectTypeBySubjectId($subject_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT `aliasName` FROM `subjects_aliases` WHERE id=? LIMIT 1;");
    $exec = $stmt->execute(array($subject_id));

    if (!$exec) {
        return [];
    }

    return $stmt->fetch();
}

function getSubjectAliasNameByScheduleCoordinates($semester, $week, $day, $class_number) {
    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    return getSubjectAliasNameBySubjectNameId($result_schedule["subject_id"]);
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

function addAudio($semester, $week, $day, $class_number, $user_id, $url) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $stmt_audio = $dbh->prepare("INSERT INTO `subjects_audios` (`schedule_id`, `user_id`, `url`) VALUES(?, ?, ?);");
    $exec_audio = $stmt_audio->execute(array($result_schedule["id"], $user_id, $url));

    return $exec_audio;
}

function addAttachment($semester, $week, $day, $class_number, $user_id, $url) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $stmt = $dbh->prepare("INSERT INTO `subjects_attachments` (`schedule_id`, `user_id`, `url`) VALUES(?, ?, ?);");
    $exec = $stmt->execute(array($result_schedule["id"], $user_id, $url));

    return $exec;
}

function addComment($semester, $week, $day, $class_number, $user_id, $content) {
    global $dbh;

    $result_schedule = getScheduleRowByCoordinates($semester, $week, $day, $class_number);

    $stmt_comment = $dbh->prepare("INSERT INTO `subjects_comments` (`schedule_id`, `user_id`, `content`) VALUES (?, ?, ?);");
    $exec_comment = $stmt_comment->execute(array($result_schedule["id"], $user_id, $content));

    return $exec_comment;
}

function sendNews($user_id, $content, $heading) {
    global $dbh;

    $stmt_news = $dbh->prepare("INSERT INTO `news` (`user_id`, `content`, `heading`) VALUES (?, ?, ?);");
    $exec_news = $stmt_news->execute(array($user_id, $content, $heading));

    return $exec_news;
}

function getLastNews() {
    global $dbh;

    $stmt_news = $dbh->prepare("SELECT * FROM `news` ORDER BY `timestamp` DESC LIMIT 5;");
    $exec_news = $stmt_news->execute();

    if (!$exec_news) {
        return [];
    }

    return $stmt_news->fetchAll();
}

function getNewsFromId($id) {
    global $dbh;

    $stmt_news = $dbh->prepare("SELECT * FROM `news` WHERE `id` < ? ORDER BY `timestamp` DESC LIMIT 5;");
    $exec_news = $stmt_news->execute(array($id));

    if (!$exec_news) {
        return [];
    }

    return $stmt_news->fetchAll();
}

function logUser($user_id, $url, $user_ip) {
    global $dbh;

    $stmt_log = $dbh->prepare("INSERT INTO `user_visits` (`user_id`, `url`, `ip`) VALUES (?, ?, ?);");
    $exec_log = $stmt_log->execute(array($user_id, $url, $user_ip));

    return $exec_log;
}

function logGuest($url, $guest_ip) {
    global $dbh;

    $stmt_log = $dbh->prepare("INSERT INTO `guest_visits` (`url`, `ip`) VALUES (?, ?);");
    $exec_log = $stmt_log->execute(array($url, $guest_ip));

    return $exec_log;
}

function decreaseNumberOfNextSubjects($subject_id, $number) {
    global $dbh;

    $stmt = $dbh->prepare("UPDATE `ssau_schedule` SET number = number - 1 WHERE subject_id=? AND number>?;");
    $exec = $stmt->execute(array($subject_id, $number));

    return $exec;
}

function deleteSubjectByRowId($row_id) {
    global $dbh;

    $stmt = $dbh->prepare("DELETE FROM `ssau_schedule` WHERE id=?;");
    $exec = $stmt->execute(array($row_id));

    return $exec;
}

function getSubjectsListBySemester($semester) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `subjects_aliases` WHERE semester=? ORDER BY id ASC;");
    $exec = $stmt->execute(array($semester));

    if (!$exec) {
        return [];
    }

    return $stmt->fetchAll();
}

function incrementSubjectsNumberAfterCoordinate($semester, $week, $day, $class_number, $subject_id) {
    global $dbh;

    $stmt = $dbh->prepare("UPDATE `ssau_schedule` SET number = number + 1 WHERE subject_id=? AND semester=? AND (week>? OR (week=? AND day>?) OR (week=? AND day=? AND class_number>?));");
    $exec = $stmt->execute(array($subject_id, $semester, $week, $week, $day, $week, $day, $class_number));

    return $exec;
}

function getLastSubjectNumberBeforeCoordinate($semester, $week, $day, $class_number, $subject_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT MAX(number) FROM `ssau_schedule` WHERE subject_id=? AND semester=? AND (week<? OR (week=? AND day<?) OR (week=? AND day=? AND class_number<?));");
    $exec = $stmt->execute(array($subject_id, $semester, $week, $week, $day, $week, $day, $class_number));

    if (!$exec) {
        return 0;
    }

    $result = $stmt->fetch();

    if (empty($result)) {
        return 0;
    }

    return $result[0];
}

function insertSubjectByCoordinatesAndType($semester, $week, $day, $class_number, $subject_id, $lecturer, $room) {
    global $dbh;
    global $semester_times;

    $last_number = getLastSubjectNumberBeforeCoordinate($semester, $week, $day, $class_number, $subject_id);

    if ($last_number === "NULL") {
        $last_number = 0;
    }

    $stmt = $dbh->prepare("INSERT INTO `ssau_schedule` (`subject_id`, `number`, `semester`, `week`, `day`, `class_number`, `time_start`, `time_end`, `lecturer`, `room`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $exec = $stmt->execute(array($subject_id, intval($last_number) + 1, $semester, $week, $day, $class_number, semesterTimeGetStart($semester_times[$semester][$class_number]), semesterTimeGetEnd($semester_times[$semester][$class_number]), $lecturer, $room));

    return $exec;
}

function checkIfCellFilled($semester, $week, $day, $class_number) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `ssau_schedule` WHERE semester=? AND week=? AND day=? AND class_number=?;");
    $exec = $stmt->execute(array($semester, $week, $day, $class_number));

    if (!$exec) {
        return false;
    }

    $result = $stmt->fetch();

    return !empty($result);
}

function deleteRowFromTableByScheduleId($semester, $week, $day, $class_number, $table_name) {
    global $dbh;

    $stmt_schedule = $dbh->prepare("SELECT * FROM `ssau_schedule` WHERE semester=? AND week=? AND day=? AND class_number=?;");
    $exec_schedule = $stmt_schedule->execute(array($semester, $week, $day, $class_number));

    if (!$exec_schedule) {
        return false;
    }

    $schedule_row = $stmt_schedule->fetch();

    if (!$schedule_row || empty($schedule_row) || !array_key_exists("id", $schedule_row)) {
        return false;
    }
    
    $schedule_id = $schedule_row["id"];
    
    $stmt = $dbh->prepare("DELETE FROM `" . $table_name . "` WHERE schedule_id=?;");
    $exec = $stmt->execute(array($schedule_id));
    
    return $exec;
}

function deleteAllAttachmentsFromScheduleCell($semester, $week, $day, $class_number) {
    deleteRowFromTableByScheduleId($semester, $week, $day, $class_number, "subjects_audios");
    deleteRowFromTableByScheduleId($semester, $week, $day, $class_number, "subjects_attachments");
    deleteRowFromTableByScheduleId($semester, $week, $day, $class_number, "subjects_comments");
    deleteRowFromTableByScheduleId($semester, $week, $day, $class_number, "subjects_videos");
}