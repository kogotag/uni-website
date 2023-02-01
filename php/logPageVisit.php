<?php
require_once 'auth.php';
require_once 'databaseQueries.php';

if (isLoggedIn()) {
    logUser($_SESSION["user_id"], basename($_SERVER['PHP_SELF']), $_SERVER["REMOTE_ADDR"]);
} else {
    logGuest(basename($_SERVER['PHP_SELF']), $_SERVER["REMOTE_ADDR"]);
}