<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<?php require_once 'php/databaseQueries.php'; ?>
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
        <link rel="icon" type="image/x-icon" href="img/logo.svg">
    </head>
    <body class="bg-light">
        <?php require 'navbar.php'; ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" id="forumBreadCrumb"></ol>
        </nav>
        <div class="container-fluid">
            <div class="row mb-2 mt-4">
                <div class="col-1"></div>
                <div class="col-10 pl-1 pr-4">
                    <textarea class="form-control mb-2" id="forumTextArea" rows="10" placeholder="Введите сообщение..."></textarea>
                    <div class="btn btn-primary" id="forumButtonSend">Отправить</div>
                </div>
                <div class="col-1"></div>
            </div>
        </div>
        <?php require 'footer.php'; ?>
        <?php require 'php/importImportantJsScripts.php'; ?>
        <script src="/js/forumSendPost.js"></script>
    </body>
</html>