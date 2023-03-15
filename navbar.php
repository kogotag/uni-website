<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<?php require_once 'php/semesterDates.php'; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <div class="mr-2">
            <img class="img-fluid" width="32px" height="auto" src="img/logo.svg"/>
        </div>
        <span class="navbar-brand text-light">Сайт 1105</span>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link text-light" href="/">Главная</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-light" href="" role="button" data-toggle="dropdown" aria-expanded="false">
                        Расписание
                    </a>
                    <div class="dropdown-menu bg-primary px-2">
                        <a class="nav-link text-light" href="/schedule.php">Наше расписание</a>
                        <a class="nav-link text-light" href="https://ssau.ru/rasp?groupId=531233720&selectedWeek=<?php echo $SM_week; ?>&selectedWeekday=<?php echo $SM_day; ?>">ssau.ru сегодня</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="/media.php">Медиа</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown mr-2">
                        <a class="text-white nav-link dropdown-toggle" href="" role="button" data-toggle="dropdown" aria-expanded="false">
                            <?php echo $_SESSION["user_name"]; ?>
                        </a>
                        <div class="dropdown-menu bg-primary">
                            <a class="nav-link text-light" href="/personal.php">Личный кабинет</a>
                        </div>
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