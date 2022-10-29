<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/index.php">Главная</a></li>
                <li class="breadcrumb-item active">Медиа</li>
            </ol>
        </nav>

        <div class="container-fluid px-2 mb-4">
            <a href="/media">Все аудио</a><br>
            <a href="https://www.youtube.com/playlist?list=PL4f6-h4zVq29EyYmdNL518uwOgdtZzWuA">Записи лекций по истории</a><br>
            <?php if (isLoggedIn() && $_SESSION["user_from_group"] === 1): ?>
                <a href="https://www.youtube.com/playlist?list=PLcsiGEUf6YIH4Vi0dDqD3f-BKpoZ4QXYu">Записи лекций по профкультуре</a><br>
            <?php endif; ?>
        </div>

        <?php require 'footer.php'; ?>
        <script src="js/mainscript.js"></script>
        <?php require 'php/importImportantJsScripts.php'; ?>
    </body>
</html>
