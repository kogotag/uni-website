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
                <div class="col-1 pr-0">
                </div>
                <div class="col-10 px-0">
                    <h4 class="mb-4">Новости</h4>
                    <div id="news">
                        <?php require 'php/getNews.php'; ?>
                    </div>
                </div>
                <div class="col-1 pl-0">
                </div>
            </div>
        </div>
        <?php require 'footer.php'; ?>
        <script src="js/jquery-3.6.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>