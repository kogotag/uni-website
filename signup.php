<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo createToken(); ?>">
        <title>Регистрация</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <?php require 'navbar.php'; ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/index.php">Главная</a></li>
                <li class="breadcrumb-item active">Регистрация</li>
            </ol>
        </nav>
        <h1 class="text-center my-4">Регистрация</h1>
        <div class="container-fluid">
            <div class="row">
                <div class="col"></div>
                <div class="col-lg-6">
                    <div class="container px-0 pt-0 pb-3 text-left text-info"><span class="text-danger font-weight-bold">! </span>Система регистрации ещё в процессе разработки. Зайдите сюда позже.</div>
                    <div id="errors" class="container px-0 py-0"></div>
                    <form id="formRegister" class="mx-0" style="width: 100%;">
                        <div class="form-group my-0">
                            <label for="login">Логин</label>
                            <div class="input-group mb-3">
                                <input class="form-control" type="text" name="login" id="login" placeholder="login"><br>
                            </div>
                        </div>
                        <div class="form-group my-0">
                            <label for="email">Электронная почта</label>
                            <div class="input-group mb-3">
                                <input class="form-control" type="email" name="email" id="email" placeholder="name@example.com"><br>
                            </div>
                        </div>
                        <div class="form-group my-0">
                            <label for="name">Имя</label>
                            <div class="input-group mb-3">
                                <input class="form-control" type="text" name="name" id="name" placeholder="Вася Пупкин"><br>
                            </div>
                        </div>
                        <div class="form-group my-0">
                            <label for="password">Пароль</label>
                            <div class="input-group mb-3">
                                <input class="form-control" type="password" name="password" id="password" placeholder="Пароль"><br>
                            </div>
                        </div>
                        <div class="form-group my-0">
                            <label for="password_repeat">Повторите пароль</label>
                            <div class="input-group mb-3">
                                <input class="form-control" type="password" name="password_repeat" id="password_repeat" placeholder="Пароль"><br>
                            </div>
                        </div>
                        <div class="btn btn-primary" id="registerbtn">Зарегистрировать</div>
                    </form>
                </div>
                <div class="col"></div>
            </div>
        </div>

        <?php require 'footer.php'; ?>
        <script src="js/mainscript.js"></script>
        <script src="js/jquery-3.6.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>