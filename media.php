<?php require_once 'php/config.inc.php'; ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="pragma" content="no-cache" />
        <title>1105 Сайт</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <?php require 'navbar.php'; ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/index.php">Главная</a></li>
                <li class="breadcrumb-item active">Медиа</li>
            </ol>
        </nav>
        
        <div>
            <a href="/media">Все аудио</a><br>
            <a href="https://www.youtube.com/playlist?list=PL4f6-h4zVq29EyYmdNL518uwOgdtZzWuA">Записи лекций по истории</a><br>
        </div>

        <script src="js/mainscript.js"></script>
        <script src="js/jquery-3.6.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
