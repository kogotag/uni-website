<?php

require_once 'russianDateFormatter.php';

try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    $stmt_news = $dbh->prepare("SELECT * FROM `news` ORDER BY `timestamp` DESC;");
    $exec_news = $stmt_news->execute();
    $news_list = [];
    if ($exec_news) {
        $news_list = $stmt_news->fetchAll();
    }

    foreach ($news_list as $news_post) {
        $stmt_user = $dbh->prepare("SELECT * FROM `users` WHERE id=?;");
        $exec_user = $stmt_user->execute(array($news_post["user_id"]));
        $user_name = $exec_user ? $stmt_user->fetch()["name"] : "Not Found";
        $timestamp = new DateTime($news_post["timestamp"]);
        echo '<div class="bg-white px-3 py-3 my-1"><small class="font-weight-bold">', $user_name, '</small>&nbsp;<small class="text-muted">', getTimeElapsed($timestamp), '</small><br><h5>', $news_post["heading"], '</h5><p class="mt-3">', $news_post["content"], "</p></div>";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}

