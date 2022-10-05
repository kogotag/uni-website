<?php require_once 'php/config.inc.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Вход</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <h1 class="text-center my-4">Вход</h1>
        <div class="d-flex justify-content-center mt-4">
            <form action="php/auth-check.php" method="post">
                <div class="form-group my-0">
                    <label for="login">Логин</label>
                    <input class="form-control" type="text" name="login" id="login" placeholder="login"><br>
                </div>
                <div class="form-group my-0">
                    <label for="password">Пароль</label>
                    <input class="form-control" type="password" name="password" id="password" placeholder="Пароль"><br>
                </div>
                <button class="btn btn-primary" type="submit">Войти</button>
            </form>
        </div>

        <script src="js/jquery-3.6.1.min.js"/>
        <script src="js/bootstrap.min.js"/>
    </body>
</html>