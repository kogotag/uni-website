<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<?php require_once 'php/semesterDates.php'; ?>
<?php require_once 'php/logPageVisit.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo createToken(); ?>">
        <title>1105 Сайт</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body class="bg-light">
        <?php require 'navbar.php'; ?>
        <div class="container-fluid px-2 py-2">
            <h3>Личный кабинет</h3>
            <?php if (isLoggedIn()): ?>
                <p>Личный кабинет в разработке</p>
            <?php else: ?>
                <p>Войдите в аккаунт или зарегистрируйтесь, чтобы получить доступ к личному кабинету</p>
            <?php endif; ?>
        </div>
        <?php require 'footer.php'; ?>
        <?php require 'php/importImportantJsScripts.php'; ?>
        <script src="js/index_page_news.js"></script>
    </body>
</html>