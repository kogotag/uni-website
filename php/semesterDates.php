<?php

require_once 'russianDateFormatter.php';

$SM_date_now = new DateTime();
$SM_semester_auto = intval($SM_date_now->format("Y")) - 2021;
$SM_semester_start = DateTime::createFromFormat("d.m.Y", "01.09." . ($SM_semester_auto + 2021));
$SM_first_september_weekday = $SM_semester_start->format("N");
$SM_date_first_monday = clone $SM_semester_start;
$SM_date_first_monday->modify("-" . $SM_first_september_weekday - 1 . " days");
$SM_week = floor($SM_date_now->diff($SM_date_first_monday)->days / 7) + 1;
$SM_day = $SM_date_now->diff($SM_date_first_monday)->days - ($SM_week - 1) * 7 + 1;
