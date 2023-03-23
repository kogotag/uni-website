<?php

define("DOMAIN_NAME", "mehaniki05.ru");

//Database credentials
define("DB_HOST", "localhost");
define("DB_DATABASE", "uni_website");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "root");

//SMTP
define("SEND_EMAILS", true);
define("SMTP_FROM", "postmaster@mehaniki05.ru");
define("SMTP_FROMNAME", "Mehaniki 1105");

//
define("SMTP_VERIFICATIONS_PER_DAY", 3);
define("LOGIN_MAX_ATTEMPTS_PER_HOUR", 10);
define("VERIFY_PAGE_MAX_VISITS_PER_HOUR", 10);
define("PASSWORD_RESET_ATTEMPT_MAX_PER_DAY", 1);
define("PASSWORD_RESET_EMAIL_REQUEST_MAX_PER_DAY", 3);

define("CSRF_TOKEN_SECRET", "fghfghfh2");

define("MAX_AUDIO_FILE_SIZE", 300 * 1024 * 1024);
define("FORUM_MAX_POST_SIZE", 20000);
define("FORUM_MESSAGES_PER_PAGE", 20);

define("WEB_SERVER_FOLDER", "/var/www/html");
define("FILES_FOLDER_REFERENCE", "files");

//Time settings
date_default_timezone_set("Europe/Moscow");

//Cookie settings
session_set_cookie_params(['samesite' => 'strict']);
session_start();