<?php

$semester_times = array(
    1 => array(
        1 => "8:00<br>9:35",
        2 => "9:45<br>11:20",
        3 => "11:30<br>13:05",
        4 => "13:30<br>15:05",
        5 => "15:15<br>16:50",
        6 => "17:00<br>18:35",
        7 => "18:45<br>20:15",
        8 => "20:25<br>21:55"
    ),
    2 => array(
        1 => "8:00<br>9:35",
        2 => "9:45<br>11:20",
        3 => "11:30<br>13:05",
        4 => "13:30<br>15:05",
        5 => "15:15<br>16:50",
        6 => "17:00<br>18:35",
        7 => "18:45<br>20:15",
        8 => "20:25<br>21:55"
    ));

function semesterTimeGetStart($time) {
    $split = explode("<br>", $time);
    
    if (empty($split) || count($split) < 2) {
        return "";
    }
    
    return $split[0];
}

function semesterTimeGetEnd($time) {
    $split = explode("<br>", $time);
    
    if (empty($split) || count($split) < 2) {
        return "";
    }
    
    return $split[1];
}