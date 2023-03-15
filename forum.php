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
                <div class="col-xl-1"></div>
                <div class="col-xl-1 col-lg-2 col-sm-3 col-xs-4 pl-0 pr-md-1 pr-sm-4">
                    <div class="container bg-white pl-1 pl-lg-3 pr-1 py-1">
                        <h5>Последние сообщения</h5>
                        <small class="text-info">Пока не работает</small>
                    </div>
                </div>
                <div class="col-xl-10 col-lg-10 col-sm-9 col-xs-8 pl-0 pr-sm-4">
                    <div class="bg-white pl-1 pl-lg-3 pr-1 py-1">
                        <h5 id="forumHeader"></h5>
                        <small class="text-info">Форум всё ещё на стадии разработки. Большая часть функционала может не работать</small>
                        <hr>
                        <div class="px-2 py-2" id="forumBody"></div>
                    </div>
                    <div id="forumPagination" class="py-2"></div>
                    <div id="forumPlaceForSendButton"></div>
                </div>
                <div class="col-lg-1"></div>
            </div>
        </div>
        <?php require 'footer.php'; ?>
        <?php require 'php/importImportantJsScripts.php'; ?>
        <script src="js/forum.js"></script>
    </body>
</html>