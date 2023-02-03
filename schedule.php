<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
<?php require_once 'php/databaseQueries.php'; ?>
<?php require_once 'php/semesterTime.php'; ?>
<?php require_once 'php/semesterDates.php'; ?>
<?php require_once 'php/logPageVisit.php'; ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo createToken(); ?>">
        <meta http-equiv="pragma" content="no-cache" />
        <title>1105 Сайт</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <?php require 'navbar.php'; ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/index.php">Главная</a></li>
                <li class="breadcrumb-item active">Расписание</li>
            </ol>
        </nav>
        <?php
        try {
            //TODO: clean this garbage later and refactor
//            $date_now = new DateTime();
//            $semester_auto = intval($date_now->format("Y")) - 2021;
//            $semester_start = DateTime::createFromFormat("d.m.Y", "01.09." . ($semester_auto + 2021));
//            $first_september_weekday = $semester_start->format("N");
//            $date_first_monday = $semester_start->modify("-" . $first_september_weekday - 1 . " days");
//            $difference_weeks = floor($date_now->diff($date_first_monday)->days / 7) + 1;
            //TODO: refactor
            $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
            if (!filter_input_array(INPUT_GET)) {
                //trychange: $semester_auto -> $SM_current_semester
                //$difference_weeks -> getWeeksFromSemesterStart($SM_current_semester)
                header("Location: ?semester=" . $SM_current_semester . "&week=" . getWeeksFromSemesterStart($SM_current_semester));
            } else {
                if (filter_input(INPUT_GET, "semester") && filter_input(INPUT_GET, "week")) {
                    $semester = htmlspecialchars(filter_input(INPUT_GET, "semester"));
                    
                    if ($semester > count($SM_date_semester_list)) {
                        header("Location: /errorPage.php?message=semesterRange");
                    }
                    
                    $week = htmlspecialchars(filter_input(INPUT_GET, "week"));
                    $date_current_week_monday = getSemesterFirstMonday($semester)->modify("+" . $week - 1 . " weeks");
                    $date_weekday_iterator = clone $date_current_week_monday;
                    ?>
                    <div class="container-fluid">
                        <div class="d-flex mx-2 justify-content-center">
                            <div class="d-flex flex-column align-items-center">
                                <div class="d-flex">
                                    <div class="dropdown">
                                        <button class="btn btn-lg dropdown-toggle font-weight-bold px-1 py-1" type="button" id="dropdown-semester" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Семестр <?php echo $semester; ?>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdown-semester">
                                            <?php
                                            for ($i = 1; $i <= $SM_semesters_count; $i++) {
                                                echo "<li><a class=\"dropdown-item";

                                                if ($i === intval($semester)) {
                                                    echo " active";
                                                }

                                                if ($i > count($SM_date_semester_list)) {
                                                    echo " disabled";
                                                }

                                                echo "\" href=\"/schedule.php?semester=" . $i . "&week=1\">Семестр " . $i . "</a></li>\n";
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-lg dropdown-toggle font-weight-bold px-1 py-1" type="button" id="dropdown-week" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Неделя <?php echo $week; ?>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdown-week">
                                            <?php
                                            for ($i = 1; $i <= getSemesterWeeksCount($semester); $i++) {
                                                echo "<li><a class=\"dropdown-item";

                                                if ($i === intval($week)) {
                                                    echo " active";
                                                }

                                                echo "\" href=\"/schedule.php?semester=" . $semester . "&week=" . $i . "\">Неделя " . $i . "</a></li>\n";
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                                <?php if (intval($semester) === intval($SM_current_semester) && intval($week) === intval(getWeeksFromSemesterStart($SM_current_semester))) : ?>
                                    <h5>Текущая неделя</h5>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Время</th>
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
                                for ($number = 1; $number <= 8; $number++) {
                                    echo '<tr>';
                                    echo '<td>', $semester_times[$semester][$number], '</td>';

                                    $subjects = getScheduleSubjectWithClassNumberBySemesterAndWeek($semester, $week, $number);

                                    for ($day = 1; $day <= 6; $day++) {
                                        $subject = $subjects[$day - 1];
                                        echo '<td id="selectableCell" class="day', $day, ' number', $number, '">';
                                        echo getSubjectAliasNameBySubjectNameId($subject["subject_id"]), "&nbsp;", $subject["number"], '<br>', $subject['lecturer'], '<br>', $subject['room'];
                                        echo '</td>';
                                    }
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="container-fluid px-3 py-2">
                        <a href="https://ssau.ru/rasp?groupId=531233720&selectedWeek=<?php echo $week; ?>&selectedWeekday=1">ssau.ru расписание на эту неделю</a>
                        <p class="text-danger mb-0">Внимание! В настоящий момент расписание обновляется вручную! Смотрите актуальное расписание на сайте университета, а здесь записи занятий.</p>
                        <p class="text-muted mb-0">Выберите предмет, чтобы получить информацию</p>
                        <?php if (isLoggedIn() && $_SESSION["user_from_group"] == 1): ?>
                            <h2 class="mt-2">Описание</h2>
                            <?php if ($_SESSION["user_admin_rank"] == 1): ?>
                                <div class="btn btn-primary mt-2 mr-2" id="openDescModal" data-toggle="modal" data-target="#descModal">
                                    Изменить описание
                                </div>
                                <div class="modal fade" id="descModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Изменить описание</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="container-fluid">
                                                    <div id="changeDescInfo"></div>
                                                    <form class="mx-0">
                                                        <div class="form-group my-0">
                                                            <label for="hwFromThisDayInput">Домашнее задание, которое задали в этот день</label>
                                                            <input type="text" id="hwFromThisDayInput" name="hwFromThisDayInput">
                                                        </div>
                                                    </form>
                                                    <div class="btn btn-primary mt-2 mr-2" id="changeDescButton">
                                                        Отправить
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div id="description" class="my-2"></div>
                            <h2 class="mt-2">Медиа</h2>
                            <div id="media" class="my-2"></div>
                            <div id="sendMediaInfo"></div>
                            <?php if ($_SESSION["user_admin_rank"] == 1): ?>
                                <div class="btn btn-primary mt-2 mr-2" id="openAudioModal" data-toggle="modal" data-target="#audioModal">
                                    Добавить аудио
                                </div>
                                <div class="modal fade" id="audioModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Добавить аудио</h5>
                                            </div>
                                            <div class="modal-body">
                                                <input type="file" id="audioInput">
                                                <div class="container-fluid px-0 mt-1">
                                                    <progress id="uploadBar" value="0" max="100"></progress>
                                                </div>
                                                <div class="btn btn-primary mt-2 mr-2" id="sendAudio">
                                                    Отправить
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn btn-primary mt-2 mr-2" id="openVideoModal" data-toggle="modal" data-target="#videoModal">
                                    Добавить видео
                                </div>
                                <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Добавить видео</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="container-fluid px-2">
                                                    <form>
                                                        <div class="form-group my-0">
                                                            <label for="videoUrlInput">Ссылка</label>
                                                            <input type="text" id="videoUrlInput" name="videoUrlInput">
                                                        </div>
                                                    </form>

                                                    <div class="btn btn-primary mt-2 mr-2" id="sendVideo">
                                                        Отправить
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn btn-primary mt-2 mr-2" id="openAttachmentModal" data-toggle="modal" data-target="#attachmentModal">
                                    Добавить вложение
                                </div>
                                <div class="modal fade" id="attachmentModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Добавить вложение</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="container-fluid px-2">
                                                    <input type="file" id="attachmentInput">
                                                    <div class="container-fluid px-0 mt-1">
                                                        <progress id="uploadBarAttachment" value="0" max="100"></progress>
                                                    </div>
                                                    <br>
                                                    <div class="btn btn-primary mt-2 mr-2" id="sendAttachmentButton">
                                                        Отправить
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <h2 class="mt-2">Комментарии</h2>
                            <div id="sendCommentInfo"></div>
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <textarea class="form-control" id="commentTextArea" rows="6" placeholder="Введите сообщение..."></textarea>
                                    <div class="btn btn-primary mt-2" id="sendComment">Отправить</div>
                                </div>
                            </div>
                            <div id="comments" class="my-2"></div>
                        <?php else: ?>
                            <h2 class="mt-2">Описание</h2>
                            <div id="description" class="my-2"></div>
                            <h2 class="mt-2">Медиа</h2>
                            <div id="media" class="my-2"></div>
                            <h2 class="mt-2">Комментарии</h2>
                            <div id="comments" class="my-2"></div>
                        <?php endif; ?>
                    </div>
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

        <?php require 'footer.php'; ?>
        <script src="js/schedule_table.js"></script>
        <?php require 'php/importImportantJsScripts.php'; ?>
    </body>
</html>
