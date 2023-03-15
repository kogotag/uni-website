<?php

require_once 'utils.php';
require_once 'auth.php';
require_once 'databaseQueries.php';

function explodeFileName($fileFullName) {
    $fileNameParts = explode(".", $fileFullName);
    $fileName = "";
    $fileExtension = "";

    if (empty($fileNameParts)) {
        echo "Ошибка формата имени файла";
        exit();
    } else if (count($fileNameParts) === 1) {
        $fileName = $fileFullName;
        $fileExtension = "";
    } else {
        $count = count($fileNameParts);
        $fileExtension = $fileNameParts[$count - 1];
        for ($i = 0; $i < $count - 2; $i++) {
            $fileName .= $fileNameParts[$i] . ".";
        }
        $fileName .= $fileNameParts[$count - 2];
    }

    return array($fileName, $fileExtension);
}

function isFileSameAsExisting($fullpath, $size) {
    if (!file_exists($fullpath)) {
        return false;
    }

    if (filesize($fullpath) === $size) {
        return true;
    }

    return false;
}

if (!isLoggedIn()) {
    echo 'Войдите, чтобы отправить сообщение';
    exit();
}

$csrf_token = htmlspecialchars(trim(filter_input(INPUT_POST, 'csrf-token')));

if ($csrf_token == null || !validateToken($csrf_token)) {
    echo "Ошибка безопасности: csrf-token не прошёл валидацию";
    exit();
}

$user_id = $_SESSION["user_id"];
$file = $_FILES["file"];
$fileFullName = htmlspecialchars($file["name"]);

if (str_contains($fileFullName, "/")) {
    echo "Имя файла не должно содержать символа &#47;";
    exit();
}

if ($fileFullName == null || strlen($fileFullName) === 0 || strlen($fileFullName) > 255) {
    echo "Некорректное имя файла";
    exit();
}

if (!forumUploadCheckDailyQuota($user_id)) {
    echo "Ваш дневной лимит загрузки на форум исчерпан. Попробуйте загрузить завтра или обратитесь к администратору";
    exit();
}

if (intval($file["size"]) > FORUM_MAX_IMAGE_SIZE) {
    echo "Максимальный размер загружаемого изображения: " . FORUM_MAX_IMAGE_SIZE / 1024 / 1024 . " Мбайт";
    exit();
}

$allowedFileExtensions = array("jpg", "jpeg", "png");

$explode = explodeFileName($fileFullName);
$fileName = $explode[0];
$fileExtension = $explode[1];

if (!in_array(strtolower($fileExtension), $allowedFileExtensions)) {
    echo "Разрешённые расширения файла: ";
    foreach ($allowedFileExtensions as $ext) {
        echo $ext, " ";
    }

    exit();
}

$upload_dir = WEB_SERVER_FOLDER . "/" . FILES_FOLDER_REFERENCE . "/" . $user_id . "/";

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755);
}

$duplicate = false;
$target_file = $upload_dir . $fileFullName;
$newFileName = $fileName;
$i = 1;
while (true) {
    if (!file_exists($target_file)) {
        break;
    }

    if (isFileSameAsExisting($target_file, $file["size"])) {
        $duplicate = true;
        break;
    }

    $target_file = $upload_dir . $fileName . strval($i) . ($fileExtension ? ("." . $fileExtension) : "");
    $newFileName = $fileName . strval($i);
    $i++;
}

if ($duplicate) {
    $fileFullName = $newFileName . ($fileExtension ? ("." . $fileExtension) : "");
    $dirReference = "/" . FILES_FOLDER_REFERENCE . "/" . $user_id . "/";
    $id = null;
    $dbSeek = forumFindUploadByFileName($fileFullName);

    if (!$dbSeek) {
        $dbUpdate = forumUploadRemember($user_id, $fileFullName, $dirReference, intval($file["size"]));
        $id = $dbUpdate;
    } else {
        $id = $dbSeek["id"];
    }

    $result = [];
    $result["file_name"] = $dirReference . $fileFullName;
    $result["id"] = $id;
    echo json_encode($result);
    exit();
}

if (!move_uploaded_file($file["tmp_name"], $target_file)) {
    echo "Ошибка: файл не был загружен";
    exit();
}

$fileFullName = $newFileName . ($fileExtension ? ("." . $fileExtension) : "");
$dirReference = "/" . FILES_FOLDER_REFERENCE . "/" . $user_id . "/";

$dbUpdate = forumUploadRemember($user_id, $fileFullName, $dirReference, intval($file["size"]));

if ($dbUpdate) {
    $result = [];
    $result["file_name"] = $dirReference . $fileFullName;
    $result["id"] = $dbUpdate;
    echo json_encode($result);
}