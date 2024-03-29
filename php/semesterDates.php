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

$SM_week = getWeeksFromSemesterStart($SM_current_semester);
$SM_day = $SM_date_now->diff(getSemesterFirstMonday($SM_current_semester))->days - ($SM_week - 1) * 7 + 1;

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

function getSemesterWeeksCount($semester) {
    global $SM_date_semester_list;

    if (!$SM_date_semester_list[$semester + 1]) {
        return 30;
    }

    return ceil($SM_date_semester_list[$semester + 1]->diff($SM_date_semester_list[$semester])->days / 7);
}