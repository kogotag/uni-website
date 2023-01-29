<?php

require_once 'russianDateFormatter.php';
$SM_semesters_count = 8;

//manually created semester dates data
//should it be this way?
$SM_date_semester_list = [];
$SM_date_semester_list[1] = DateTime::createFromFormat("d.m.Y", "01.09.2022");
$SM_date_semester_list[2] = DateTime::createFromFormat("d.m.Y", "06.02.2023");
$SM_date_semester_list[3] = DateTime::createFromFormat("d.m.Y", "01.09.2023");

$SM_date_now = new DateTime();
$SM_current_semester = 1;

for ($i = 1; $i <= $SM_semesters_count; $i++) {
    if (is_null($SM_date_semester_list[$i]) || $SM_date_now <= $SM_date_semester_list[$i]) {
        $SM_current_semester = $i - 1;
        break;
    }
}

$SM_semester_auto = intval($SM_date_now->format("Y")) - 2021;
$SM_semester_start = DateTime::createFromFormat("d.m.Y", "01.09." . ($SM_semester_auto + 2021));
$SM_first_september_weekday = $SM_semester_start->format("N");
$SM_date_first_monday = clone $SM_semester_start;
$SM_date_first_monday->modify("-" . $SM_first_september_weekday - 1 . " days");

// old: used in navbar
$SM_week = floor($SM_date_now->diff($SM_date_first_monday)->days / 7) + 1;
$SM_day = $SM_date_now->diff($SM_date_first_monday)->days - ($SM_week - 1) * 7 + 1;

//

function getWeeksFromSemesterStart($semester) {
    global $SM_date_now;
    
    return floor($SM_date_now->diff(getSemesterFirstMonday($semester))->days / 7) + 1;
}

function getSemesterFirstMonday($semester) {
    global $SM_date_semester_list;
    
    $semester_start = clone $SM_date_semester_list[$semester];
    $first_day_weekday = $semester_start->format("N");
    return $semester_start->modify("-" . $first_day_weekday - 1 . " days");
}