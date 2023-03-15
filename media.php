<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
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
        <link rel="icon" type="image/x-icon" href="img/logo.svg">
    </head>
    <body>
        <?php require 'navbar.php'; ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/index.php">Главная</a></li>
                <li class="breadcrumb-item active">Медиа</li>
            </ol>
        </nav>

        <div class="container-fluid px-2 mb-4">
            <a href="https://www.youtube.com/playlist?list=PL4f6-h4zVq29EyYmdNL518uwOgdtZzWuA">Записи лекций по истории</a><br>
            <?php if (isLoggedIn() && $_SESSION["user_from_group"] === 1): ?>
                <a href="https://www.youtube.com/playlist?list=PLcsiGEUf6YIH4Vi0dDqD3f-BKpoZ4QXYu">Записи лекций по профкультуре</a><br>
            <?php endif; ?>
            <?php if (isLoggedIn() && $_SESSION["user_from_group"] === 1): ?>
            <a href="https://drive.google.com/drive/folders/1SdMb3JlT97Ov8xnfqVD9V0agIEz9gOWF">Программирование гугл-диск</a>
            <?php else: ?>
            <a href="/errorPage.php?message=authLink">Программирование гугл-диск</a>
            <?php endif; ?>
            <h3>Механика</h3>
            <h4>Учебники</h4>
            <a href="<?php echo "/", FILES_FOLDER_REFERENCE, "/", "physics_uchebnik_irodov.pdf" ?>">Учебник Иродова</a><br>
            <a href="<?php echo "/", FILES_FOLDER_REFERENCE, "/", "physics_uchebnik_matveev.pdf" ?>">Учебник Матвеева</a><br>
            <a href="<?php echo "/", FILES_FOLDER_REFERENCE, "/", "physics_uchebnik_saveliev.pdf" ?>">Учебник Савельева</a><br>
            <a href="<?php echo "/", FILES_FOLDER_REFERENCE, "/", "physics_uchebnik_sivuhin.pdf" ?>">Учебник Сивухина</a><br>
            <a href="<?php echo "/", FILES_FOLDER_REFERENCE, "/", "physics_uchebnik_trofimova.pdf" ?>">Учебник Трофимовой</a>
            <h4>Задачники</h4>
            <a href="<?php echo "/", FILES_FOLDER_REFERENCE, "/", "physics_zadachnik_chertov.pdf" ?>">Задачник Чертова</a><br>
            <a href="<?php echo "/", FILES_FOLDER_REFERENCE, "/", "physics_zadachnik_irodov.pdf" ?>">Задачник Иродова</a>
        </div>

        <?php require 'footer.php'; ?>
        <script src="js/mainscript.js"></script>
        <?php require 'php/importImportantJsScripts.php'; ?>
    </body>
</html>
