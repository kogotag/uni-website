<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<?php require_once 'php/logPageVisit.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo createToken(); ?>">
        <title>Вход</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="icon" type="image/x-icon" href="img/logo.svg">
    </head>
    <body>
        <?php require 'navbar.php'; ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/index.php">Главная</a></li>
                <li class="breadcrumb-item active">Вход</li>
            </ol>
        </nav>
        <h1 class="text-center my-4">Вход</h1>
        <div class="container-fluid">
            <div class="row">
                <div class="col"></div>
                <div class="col-lg-6">
                    <div id="errors" class="container px-0 py-0"></div>
                    <form id="formLogin" class="mx-0" style="width: 100%;">
                        <div class="form-group my-0">
                            <label for="login">Логин</label>
                            <div class="input-group mb-3">
                                <input class="form-control" type="text" name="login" id="login" placeholder="login"><br>
                            </div>
                        </div>
                        <div class="form-group my-0 mb-2">
                            <label for="password">Пароль</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="password" id="password" placeholder="Пароль"><br>
                            </div>
                        </div>
                        <div class="form-group my-0 mb-2">
                            <a href="/forgotPassword.php">Забыли пароль?</a>
                        </div>
                        <div class="form-group my-0">
                            <div class="btn btn-primary" id="loginbtn">Войти</div>
                        </div>
                    </form>
                </div>
                <div class="col"></div>
            </div>
        </div>

        <?php require 'footer.php'; ?>
        <script src="js/mainscript.js"></script>
        <script src="js/signin_page_script.js"></script>
        <?php require 'php/importImportantJsScripts.php'; ?>
    </body>
</html>