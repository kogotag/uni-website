<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<?php require_once 'php/semesterDates.php'; ?>
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
                <div class="col-1 pr-0">
                </div>
                <div class="col-10 px-0">
                    <h6>Семестр <?php echo $SM_current_semester;?>, Неделя <?php echo getWeeksFromSemesterStart($SM_current_semester); ?></h6>
                    <h4 class="mb-4">Новости</h4>
                    <div id="news">
                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="btn btn-primary mt-2" id="moreNews">Ещё</div>
                    </div>
                </div>
                <div class="col-1 pl-0">
                </div>
            </div>
        </div>
        <?php require 'footer.php'; ?>
        <?php require 'php/importImportantJsScripts.php'; ?>
        <script src="js/index_page_news.js"></script>
    </body>
</html>