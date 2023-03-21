<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
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
        <div class="container-fluid">
            <div class="row mb-2 mt-4">
                <div class="col-1 pl-3 pr-1">
                    <div class="container bg-white px-1 py-1">
                        <h5>Список всяких штук</h5>
                        <div><a href="">Штука</a></div>
                        <div><a href="">Штука</a></div>
                        <div><a href="">Штука</a></div>
                        <div><a href="">Штука</a></div>
                        <div><a href="">Штука</a></div>

                    </div>
                </div>
                <div class="col-11 pl-1 pr-4">
                    <div class="bg-white pl-2 pr-1 py-1">
                        <h5>Форумы</h5>
                        <hr>
                        <div class="px-2 py-2">
                            <div class="bg-light pl-3 pr-2 py-1">
                                <a href="">Математика</a><br>
                                <p>"Три икс в кубе плюс константа. Ну что там?" (с) Берём интегралы или как довести Кирилла до слёз</p>
                            </div>
                            <div class="bg-white pl-3 pr-2 py-1">
                                <a href="">Физика</a><br>
                                <p>Мы это ищем ? А хули тангенс? А. Пхахпхах. Бля. Понял. А Стоп. Нет не понял. Противолежащий на прилежащий. А. Бля. Понял. Вахуе блять....</p>
                            </div>
                            <div class="bg-light pl-3 pr-2 py-1">
                                <a href="">Программирование</a><br>
                                <p>Lvl 1 crook - списать программирование у Коли. Lvl 35 boss - почитать гайды и сделать самому</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require 'footer.php'; ?>
        <?php require 'php/importImportantJsScripts.php'; ?>
        <script src="js/index_page_news.js"></script>
    </body>
</html>