<?php require_once 'php/config.inc.php'; ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="pragma" content="no-cache" />
        <title>1105 Сайт</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    </head>
    <body>
        <?php require 'navbar.php'; ?>
        <?php
        try {
            $date_now = new DateTime();
            $semester_auto = intval($date_now->format("Y")) - 2021;
            $semester_start = DateTime::createFromFormat("d.m.Y", "01.09." . ($semester_auto + 2021));
            $difference_weeks = floor($date_now->diff($semester_start)->days / 7) + 1;
            $first_september_weekday = $semester_start->format("N");
            $date_first_monday = $semester_start->modify("-" . $first_september_weekday - 1 . " days");
            $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
            if (!filter_input_array(INPUT_GET)) {
                header("Location: ?semester=" . $semester_auto . "&week=" . $difference_weeks); // TODO: current week
            } else {
                if (filter_input(INPUT_GET, "semester") && filter_input(INPUT_GET, "week")) {
                    $semester = htmlspecialchars(filter_input(INPUT_GET, "semester"));
                    $week = htmlspecialchars(filter_input(INPUT_GET, "week"));
                    $date_current_week_monday = $date_first_monday->modify("+" . $week - 1 . " weeks");
                    $date_weekday_iterator = $date_current_week_monday;
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/index.php">Главная</a></li>
                            <li class="breadcrumb-item active">Расписание</li>
                        </ol>
                    </nav>
                    <div class="row">
                        <div class="col-12 align-content-between">
                            <div class="d-flex mx-2 justify-content-between">
                                <a href="/schedule.php?semester=<?php echo $semester; ?>&week=<?php echo $week - 1 > 0 ? $week - 1 : 1; ?>"><h3>&lt;</h3></a>
                                <span><h3>Неделя <?php echo $week; ?></h3></span>
                                <a href="/schedule.php?semester=<?php echo $semester; ?>&week=<?php echo $week + 1; ?>"><h3>&gt;</h3></a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Понедельник<br><?php
                                        echo $date_weekday_iterator->format("d.m.Y");
                                        $date_weekday_iterator->modify("+1 days");
                                        ?></th>
                                    <th>Вторник<br><?php
                                        echo $date_weekday_iterator->format("d.m.Y");
                                        $date_weekday_iterator->modify("+1 days");
                                        ?></th>
                                    <th>Среда<br><?php
                                        echo $date_weekday_iterator->format("d.m.Y");
                                        $date_weekday_iterator->modify("+1 days");
                                        ?></th>
                                    <th>Четверг<br><?php
                                        echo $date_weekday_iterator->format("d.m.Y");
                                        $date_weekday_iterator->modify("+1 days");
                                        ?></th>
                                    <th>Пятница<br><?php
                                        echo $date_weekday_iterator->format("d.m.Y");
                                        $date_weekday_iterator->modify("+1 days");
                                        ?></th>
                                    <th>Суббота<br><?php
                                        echo $date_weekday_iterator->format("d.m.Y");
                                        $date_weekday_iterator->modify("+1 days"); //TODO: переделать через цикл по датам
                                        ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($number = 1; $number <= 6; $number++) {
                                    echo '<tr>';
                                    $stmt_row = $dbh->prepare("SELECT * FROM `ssau_schedule` WHERE semester=? AND week=? AND number=? ORDER BY `day`;");
                                    $result_row = $stmt_row->execute(array($semester, $week, $number));
                                    $subjects = [];
                                    if ($result_row) {
                                        $subjects = $stmt_row->fetchAll();
                                    }
                                    for ($day = 1; $day < count($subjects); $day++) {
                                        $subject = $subjects[$day - 1];
                                        echo '<td id="selectableCell" class="day', $day, ' number', $number, '">';
                                        echo $subject['subject_name'], '<br>', $subject['subject_lecturer'], '<br>', $subject['subject_classroom'];
                                        echo '</td>';
                                    }
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <span class="text-danger">Внимание! В настоящий момент расписание обновляется вручную! Смотрите актуальное расписание на сайте университета, а здесь записи занятий.</span><br>
                    <span class="text-muted">Выберите предмет, чтобы получить информацию</span><br>
                    <h2>Описание</h2><br>
                    <div id="description"></div><br>
                    <h2>Медиа</h2><br>
                    <div id="media"></div><br>
                    <h2>Комментарии</h2><br>
                    <div id="comments"></div><br>
                    <?php
                } else {
                    echo 'semester or week not specified';
                }
            }
        } catch (Exception $ex) {
            print "Error!: " . $ex->getMessage() . "<br>";
            die();
        }
        ?>

        <script src="js/mainscript.js"></script>
        <script src="js/schedule_table.js"></script>
        <script src="js/jquery-3.6.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
