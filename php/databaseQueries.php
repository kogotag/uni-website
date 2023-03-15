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

function getUserByEmail($email) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `users` WHERE email=?;");
    $exec = $stmt->execute(array($email));

    if (!$exec) {
        return false;
    }

    $result = $stmt->fetch();

    if (!$result) {
        return false;
    }

    if (!array_key_exists("id", $result)) {
        return false;
    }

    return $result["id"];
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

function generateResetPasswordCode($user_id) {
    global $dbh;

    $code = generateRandomString(255);
    $stmt = $dbh->prepare("INSERT INTO `reset_password` (`user_id`, `code`) VALUES (?, ?);");
    $exec = $stmt->execute(array($user_id, $code));

    if (!$exec) {
        return false;
    }

    return $code;
}

function getUserIdForResetPasswordByCode($code) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `reset_password` WHERE code=?;");
    $exec = $stmt->execute(array($code));

    if (!$exec) {
        return false;
    }

    $result = $stmt->fetch();

    if (!$result || !array_key_exists("user_id", $result)) {
        return false;
    }

    return $result["user_id"];
}

function changeUserPassword($user_id, $password_hash) {
    global $dbh;

    $stmt = $dbh->prepare("UPDATE `users` SET password_hash=? WHERE id=?;");
    $exec = $stmt->execute(array($password_hash, $user_id));

    return $exec;
}

function checkLoginAttempts($ip) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `login_attempts` WHERE `ip`=?;");
    $exec = $stmt->execute(array($ip));

    if (!$exec) {
        return false;
    }

    return $stmt->fetchAll();
}

function addLoginAttempt($ip, $login) {
    global $dbh;

    $stmt = $dbh->prepare("INSERT INTO `login_attempts` (`ip`, `login`) VALUES(?, ?);");
    $exec = $stmt->execute(array($ip, $login));

    return $exec;
}

function loginAttemptCheckUser($login) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `users` WHERE `login`=? OR `email`=?;");
    $exec = $stmt->execute(array($login, $login));

    if (!$exec) {
        return false;
    }

    return $stmt->fetch();
}

function loginSaveAuthorization($user_id, $selector, $authenticator_hash) {
    global $dbh;

    $stmt = $dbh->prepare("INSERT INTO `authorization` (`user_id`, `selector`, `token`) VALUES(?, ?, ?);");
    $exec = $stmt->execute(array($user_id, $selector, $authenticator_hash));

    return $exec;
}

function addPasswordResetAttempt($ip, $isSuccessful) {
    global $dbh;

    $stmt = $dbh->prepare("INSERT INTO `reset_password_attempts` (`ip`, `success`) VALUES(?, ?);");
    $exec = $stmt->execute(array($ip, (int) $isSuccessful));

    return $exec;
}

function passwordResetNumberAttemptsValidate($ip) {
    global $dbh;

    $timeCompare = date("Y-m-d H:i:s", strtotime("-1 day"));
    $stmt = $dbh->prepare("SELECT `id` FROM `reset_password_attempts` WHERE ip=? AND success=0 AND timestamp>?;");
    $exec = $stmt->execute(array($ip, $timeCompare));

    if (!$exec) {
        return false;
    }

    $result = $stmt->fetchAll();

    if (count($result) >= PASSWORD_RESET_ATTEMPT_MAX_PER_DAY) {
        return false;
    } else {
        return true;
    }
}

function addResetPasswordEmailRequest($ip, $user_id) {
    global $dbh;

    $stmt = $dbh->prepare("INSERT INTO `reset_password_email_requests` (`ip`, `user_id`) VALUES(?, ?);");
    $exec = $stmt->execute(array($ip, $user_id));

    return $exec;
}

function checkResetPasswordEmailRequestsNotTooOften($ip) {
    global $dbh;

    $timeCompare = date("Y-m-d H:i:s", strtotime("-1 minutes"));
    $stmt = $dbh->prepare("SELECT `id` FROM `reset_password_email_requests` WHERE ip=? AND timestamp>?;");
    $exec = $stmt->execute(array($ip, $timeCompare));

    if (!$exec) {
        return false;
    }

    $result = $stmt->fetchAll();

    if (count($result) > 0) {
        return false;
    } else {
        return true;
    }
}

function checkResetPasswordEmailRequestsDaily($ip) {
    global $dbh;

    $timeCompare = date("Y-m-d H:i:s", strtotime("-1 day"));
    $stmt = $dbh->prepare("SELECT `id` FROM `reset_password_email_requests` WHERE ip=? AND timestamp>?;");
    $exec = $stmt->execute(array($ip, $timeCompare));

    if (!$exec) {
        return false;
    }

    $result = $stmt->fetchAll();

    if (count($result) >= PASSWORD_RESET_EMAIL_REQUEST_MAX_PER_DAY) {
        return false;
    } else {
        return true;
    }
}

function forumGetForums() {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `forum_forums` ORDER BY `id`;");
    $exec = $stmt->execute(array());

    if (!$exec) {
        return [];
    }

    return $stmt->fetchAll();
}

function forumGetTopics($forum_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `forum_topics` INNER JOIN (SELECT name AS user_name, id AS user_id FROM `users`) AS temp ON forum_topics.author=temp.user_id WHERE forum=? ORDER BY `id`;");
    $exec = $stmt->execute(array($forum_id));

    if (!$exec) {
        return [];
    }

    return $stmt->fetchAll();
}

function forumGetPosts($topic_id, $page = 1) {
    global $dbh;

    try {
        $page = intval($page);
    } catch (Exception $ex) {
        return [];
    }

    $stmt = $dbh->prepare("SELECT * FROM `forum_posts` INNER JOIN (SELECT name AS user_name, id AS user_id FROM `users`) AS temp ON forum_posts.author=temp.user_id WHERE topic=? ORDER BY `id` LIMIT ? OFFSET ?;");
    $stmt->bindValue(1, $topic_id, PDO::PARAM_INT);
    $stmt->bindValue(2, FORUM_MESSAGES_PER_PAGE, PDO::PARAM_INT);
    $stmt->bindValue(3, ($page - 1) * FORUM_MESSAGES_PER_PAGE, PDO::PARAM_INT);
    $exec = $stmt->execute();

    if (!$exec) {
        return [];
    }

    return $stmt->fetchAll();
}

function forumGetTopicInfo($topic_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `forum_topics` WHERE id=?;");
    $exec = $stmt->execute(array($topic_id));

    if (!$exec) {
        return [];
    }

    return $stmt->fetch();
}

function forumGetForumInfo($forum_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `forum_forums` WHERE id=?;");
    $exec = $stmt->execute(array($forum_id));

    if (!$exec) {
        return [];
    }

    return $stmt->fetch();
}

function forumGetPostInfo($post_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `forum_posts` WHERE id=?;");
    $exec = $stmt->execute(array($post_id));

    if (!$exec) {
        return [];
    }

    return $stmt->fetch();
}

function forumBreadCrumbTopicPage($topic_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT id, forum, name, forum_id, forum_name FROM `forum_topics` INNER JOIN (SELECT id AS forum_id, name AS forum_name FROM `forum_forums`) AS temp ON forum_topics.forum=temp.forum_id WHERE id=?;");
    $exec = $stmt->execute(array($topic_id));

    if (!$exec) {
        return [];
    }

    return $stmt->fetch();
}

function forumGetForumTopicsNumber($forum_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT forum FROM `forum_topics` WHERE forum=?;");
    $exec = $stmt->execute(array($forum_id));

    if (!$exec) {
        return 0;
    }

    return count($stmt->fetchAll());
}

function forumGetTopicPostsNumber($topic_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT topic FROM `forum_posts` WHERE topic=?;");
    $exec = $stmt->execute(array($topic_id));

    if (!$exec) {
        return 0;
    }

    $posts_count = count($stmt->fetchAll());
    $pages_count = ceil($posts_count / FORUM_MESSAGES_PER_PAGE);

    $result = [];
    $result["posts_count"] = $posts_count;
    $result["pages_count"] = $pages_count;

    return $result;
}

function forumGetPostPageNumber($post_id) {
    global $dbh;

    $stmt_topic = $dbh->prepare("SELECT id, topic FROM `forum_posts` WHERE id=?;");
    $exec_topic = $stmt_topic->execute(array($post_id));

    if (!$exec_topic) {
        return 1;
    }

    $result_topic = $stmt_topic->fetch();

    if (!$result_topic) {
        return 1;
    }

    $topic_id = $result_topic["topic"];

    $stmt = $dbh->prepare("SELECT id, topic FROM `forum_posts` WHERE id < ? AND topic=?;");
    $stmt->bindValue(1, $post_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $topic_id, PDO::PARAM_INT);
    $exec = $stmt->execute();

    if (!$exec) {
        return 1;
    }

    return ceil((count($stmt->fetchAll()) + 1) / FORUM_MESSAGES_PER_PAGE);
}

function forumAddPost($text, $topic_id, $user_id) {
    global $dbh;

    $stmt = $dbh->prepare("INSERT INTO `forum_posts` (`content`, `topic`, `author`) VALUES(?, ?, ?);");
    $exec = $stmt->execute(array($text, $topic_id, $user_id));
    $id = $dbh->lastInsertId();

    if (!$exec) {
        return false;
    }

    return $id;
}

function forumEditPost($text, $post_id) {
    global $dbh;

    $stmt = $dbh->prepare("UPDATE `forum_posts` SET content=?, edits_count = edits_count + 1, last_edit_timestamp = now() WHERE id=?;");
    $exec = $stmt->execute(array($text, $post_id));

    return $exec;
}

function forumUploadRemember($user_id, $file_name, $file_dir, $file_size) {
    global $dbh;

    $stmt = $dbh->prepare("INSERT INTO `forum_files_control` (`user_id`, `file_name`, `file_dir`, `file_size`) VALUES(?, ?, ?, ?);");
    $exec = $stmt->execute(array($user_id, $file_name, $file_dir, $file_size));
    $id = $dbh->lastInsertId();

    if (!$exec) {
        return false;
    }

    return $id;
}

function forumUploadCheckDailyQuota($user_id) {
    global $dbh;

    $timeCompare = date("Y-m-d H:i:s", strtotime("-1 day"));
    $stmt = $dbh->prepare("SELECT user_id, file_size, timestamp FROM `forum_files_control` WHERE user_id=? AND timestamp>?;");
    $exec = $stmt->execute(array($user_id, $timeCompare));

    if (!$exec) {
        return false;
    }

    $size = 0;
    foreach ($stmt->fetchAll() as $row) {
        $size += intval($row["file_size"]);
    }

    if ($size >= FORUM_UPLOAD_DAILY_QUOTA) {
        return false;
    } else {
        return true;
    }
}

function forumUploadGetInfo($id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `forum_files_control` WHERE id=?;");
    $exec = $stmt->execute(array($id));

    if (!$exec) {
        return false;
    }

    return $stmt->fetch();
}

function forumFindUploadByFileName($file) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `forum_files_control` WHERE file_name=?;");
    $exec = $stmt->execute(array($file));

    if (!$exec) {
        return false;
    }

    return $stmt->fetch();
}

function forumGetPostImages($post_id) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT * FROM `forum_posts_images` INNER JOIN (SELECT id AS file_id, file_name, file_dir FROM `forum_files_control`) AS temp ON attachment=temp.file_id WHERE post=? ORDER BY number;");
    $exec = $stmt->execute(array($post_id));

    if (!$exec) {
        return [];
    }

    return $stmt->fetchAll();
}

function forumPostAddImage($user_id, $post_id, $number, $attachment_id) {
    global $dbh;

    $stmt = $dbh->prepare("INSERT INTO `forum_posts_images` (`user_id`, `post`, `number`, `attachment`) VALUES(?, ?, ?, ?);");
    $exec = $stmt->execute(array($user_id, $post_id, $number, $attachment_id));

    return $exec;
}

function forumPostDeleteImages($post_id) {
    global $dbh;
    
    $stmt = $dbh->prepare("DELETE FROM `forum_posts_images` WHERE post=?;");
    $exec = $stmt->execute(array($post_id));
    
    return $exec;
}
