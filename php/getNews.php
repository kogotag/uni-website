<?php

require_once 'russianDateFormatter.php';
require_once 'databaseQueries.php';

function printNewsList($news_list) {
    $result = "";
    foreach ($news_list as $news_post) {
        $user_name = getUserById($news_post["user_id"]);
        $timestamp = new DateTime($news_post["timestamp"]);
        $result .= '<div class="bg-white px-3 py-3 my-1"><small class="font-weight-bold">' . $user_name . '</small>&nbsp;<small class="text-muted">' . getTimeElapsed($timestamp) . '</small><br><h5>' . $news_post["heading"] . '</h5><p class="mt-3">' . $news_post["content"] . "</p></div>";
    }
    
    return $result;
}

function getMinIdFromNewsList($news_list) {
    if (empty($news_list)) {
        return 0;
    }
    
    $min = $news_list[0];
    
    foreach ($news_list as $news_post) {
        if ($news_post["id"] < $min) {
            $min = $news_post["id"];
        }
    }
    
    return $min;
}

$newsNumber = htmlspecialchars(filter_input(INPUT_POST, "newsNumber"));

$news_list = [];

if (!empty($newsNumber)) {
    if (strlen($newsNumber) > 255 || !is_numeric($newsNumber)) {
        exit();
    }

    $news_list = getNewsFromId($newsNumber);
    
    if (empty($news_list)) {
        $result = [];
        $result["id"] = $newsNumber;
        $result["news"] = "";
        echo json_encode($result);
        exit();
    }
} else {
    $news_list = getLastNews();
}

$result = [];
$result["news"] = printNewsList($news_list);
$result["id"] = getMinIdFromNewsList($news_list);

echo json_encode($result);
