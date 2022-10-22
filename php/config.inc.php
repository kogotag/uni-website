<?php

//Database credentials
define("DB_HOST", "localhost");
define("DB_DATABASE", "uni_website");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "F1f@7Lgi&Slm3");

//SMTP
define("SMTP_FROM", "postmaster@mehaniki05.ru");
define("SMTP_FROMNAME", "Mehaniki 1105");

define("SMTP_VERIFICATIONS_PER_DAY", 3);

define("CSRF_TOKEN_SECRET", "gsg6h65hdfgdfg");

//Cookie settings
session_set_cookie_params(['samesite' => 'strict']);
session_start();