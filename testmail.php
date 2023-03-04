<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
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
        <div class="container-fluid">
            <div class="row mb-2 mt-4">
                <div class="col-lg-4 pr-0"></div>
                <div class="col-lg-4 col-md-12">
                    <h4 class="text-center">Проверка почтового сервера</h4>
                    <form id="formTestEmail" class="mx-0" style="width: 100%;">
                        <div class="form-group mb-3">
                            <label for="email">Электронная почта</label>
                            <input class="form-control" type="email" name="email" id="email" placeholder="kogotag@mail.ru">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="checkResetPassword" name="checkResetPassword">
                            <label class="form-check-label" for="checkResetPassword">
                                Заодно сбросить пароль
                            </label>
                        </div>
                    </form>
                    <div class="btn btn-primary" id="btnSendEmail">Отправить письмо</div>
                </div>
                <div class="col-lg-4 pl-0"></div>
            </div>
        </div>
        <?php require 'footer.php'; ?>
        <?php require 'php/importImportantJsScripts.php'; ?>
        <script src="js/index_page_news.js"></script>
        <script src="js/testmail.js"></script>
    </body>
</html>