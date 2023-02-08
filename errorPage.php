<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<?php require_once 'php/databaseQueries.php'; ?>
<?php require_once 'php/logPageVisit.php'; ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo createToken(); ?>">
        <meta http-equiv="pragma" content="no-cache" />
        <title>1105 Сайт</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <?php require 'navbar.php'; ?>
        
        <div class="container-fluid pt-2">
        <h3>
            Ошибка
        </h3>
        <p>
            <?php
            $message = htmlspecialchars(trim(filter_input(INPUT_GET, "message")));

            if ($message === "semesterRange") {
                echo "Выбран семестр, временные рамки которого ещё не были заданы администратором сайта.";
            }
            ?>
        </p>
        <a href="javascript:history.go(-1)">Назад</a>
        </div>

        <?php require 'footer.php'; ?>
        <?php require 'php/importImportantJsScripts.php'; ?>
    </body>
</html>