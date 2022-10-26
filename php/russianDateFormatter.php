<?php

function getTimeElapsed($date) {
    $now = new DateTime();
    $diff = $now->diff($date);
    if ($diff->y !== 0) {
        return $diff->y . " " . stringYears($diff->y) . " назад";
    }

    if ($diff->m !== 0) {
        return $diff->m . " " . stringMonth($diff->m) . " назад";
    }

    if ($diff->d !== 0) {
        return $diff->d . " " . stringDays($diff->d) . " назад";
    }

    if ($diff->h !== 0) {
        return $diff->h . " " . stringHours($diff->h) . " назад";
    }

    if ($diff->i !== 0) {
        return $diff->i . " " . stringMinutes($diff->i) . " назад";
    }

    if ($diff->s !== 0) {
        return $diff->s . " " . stringSeconds($diff->s) . " назад";
    }

    return "только что";
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
