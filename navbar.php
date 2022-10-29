<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<?php require_once 'russianDateFormatter.php'; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <div class="mr-3">
            <img class="img-fluid" width="25px" height="auto" src="img/ssaulogo.svg"/>
        </div>
        <span class="navbar-brand text-light">Сайт 1105</span>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link text-light" href="/index.php">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="/schedule.php">Расписание</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="/media.php">Медиа</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item mr-2">
                        <span class="text-white navbar-text"><?php echo $_SESSION["user_name"]; ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/logout.php">Выход</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/signin.php">Войти</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/signup.php">Зарегистрироваться</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>