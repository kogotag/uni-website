<?php

function getTimeElapsed($date) {
    $now = new DateTime();
    $diff = $now->diff($date);
    if ($diff->y !== 0) {
        return $diff->y . " " . stringYears($diff->y);
    }

    if ($diff->m !== 0) {
        return $diff->m . " " . stringMonth($diff->m);
    }

    if ($diff->d !== 0) {
        return $diff->d . " " . stringDays($diff->d);
    }

    if ($diff->h !== 0) {
        return $diff->h . " " . stringHours($diff->h);
    }

    if ($diff->i !== 0) {
        return $diff->i . " " . stringMinutes($diff->i);
    }

    if ($diff->s !== 0) {
        return $diff->s . " " . stringSeconds($diff->s);
    }
}

function russianCountsDeclentions($word1, $word2, $word3, $number) {
    if (($number % 100 > 10 && $number % 100 < 15) || $number % 10 === 0 || $number % 10 > 4) {
        return $word1;
    } elseif ($number % 10 === 1) {
        return $word2;
    } else {
        return $word3;
    }
}

function stringYears($number) {
    return russianCountsDeclentions("лет", "год", "года", $number);
}

function stringMonth($number) {
    return russianCountsDeclentions("месяцев", "месяц", "месяца", $number);
}

function stringDays($number) {
    return russianCountsDeclentions("дней", "день", "дня", $number);
}

function stringHours($number) {
    return russianCountsDeclentions("часов", "час", "часа", $number);
}

function stringMinutes($number) {
    return russianCountsDeclentions("минут", "минута", "минуты", $number);
}

function stringSeconds($number) {
    return russianCountsDeclentions("секунд", "секунда", "секунды", $number);
}

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
        echo '<div class="bg-white px-3 py-3 my-1"><small class="font-weight-bold">', $user_name, '</small>&nbsp;<small class="text-muted">', getTimeElapsed($timestamp), ' назад</small><br><h5>', $news_post["heading"], '</h5><p class="mt-3">', $news_post["content"], "</p></div>";
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}

