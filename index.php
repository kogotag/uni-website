<?php require_once 'php/config.inc.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>1105 Сайт</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="mr-3">
                <img class="img-fluid" width="25px" height="auto" src="img/ssaulogo.svg"/>
            </div>
            <a class="navbar-brand" href="#">Сайт 1105</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Главная</a>
                    </li>
                </ul>
                <div class="btn-group" role="group">
                    <a class="btn btn-primary" href="signin.php">Войти</a>
                    <a class="btn btn-primary" href="signup.php">Зарегистрироваться</a>
                </div>
            </div>
        </nav>

        <script src="js/jquery-3.6.1.min.js"/>
        <script src="js/bootstrap.min.js"/>
    </body>
</html>