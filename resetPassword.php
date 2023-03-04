<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<?php require_once 'php/databaseQueries.php'; ?>
<?php
$resetId = htmlspecialchars(filter_input(INPUT_GET, 'resetId'));

if (!$resetId) {
    header("Location: /errorPage.php?message=noResetId");
}

$user_id = getUserIdForResetPasswordByCode($resetId);

if (!$user_id) {
    header("Location: /errorPage.php?message=invalidResetId");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo createToken(); ?>">
        <title>Восстановление пароля</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body class="bg-light">
        <?php require 'navbar.php'; ?>
        <div class="container-fluid">
            <div class="row mb-2 mt-4">
                <div class="col-lg-4 pr-0"></div>
                <div class="col-lg-4 col-md-12">
                    <h4 class="text-center">Восстановление пароля</h4>
                    <form id="formResetPassword" class="mx-0" style="width: 100%;">
                        <div class="form-group mb-3">
                            <label for="passwordFirst">Введите пароль</label>
                            <input class="form-control" type="password" name="passwordFirst" id="passwordFirst">
                        </div>
                        <div class="form-group mb-3">
                            <label for="passwordSecond">Повторите пароль</label>
                            <input class="form-control" type="password" name="passwordSecond" id="passwordSecond">
                        </div>
                    </form>
                    <div class="btn btn-primary" id="btnResetPassword">Сменить пароль</div>
                </div>
                <div class="col-lg-4 pl-0"></div>
            </div>
        </div>
        <?php require 'footer.php'; ?>
        <?php require 'php/importImportantJsScripts.php'; ?>
        <script src="js/reset_password.js"></script>
    </body>
</html>