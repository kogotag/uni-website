<?php require_once 'php/utils.php'; ?>
<?php require_once 'php/auth.php'; ?>
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
            $date_now = new DateTime();
            $semester_auto = intval($date_now->format("Y")) - 2021;
            $semester_start = DateTime::createFromFormat("d.m.Y", "01.09." . ($semester_auto + 2021));
            $first_september_weekday = $semester_start->format("N");
            $date_first_monday = $semester_start->modify("-" . $first_september_weekday - 1 . " days");
            $difference_weeks = floor($date_now->diff($date_first_monday)->days / 7) + 1;

            $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
            if (!filter_input_array(INPUT_GET)) {
                header("Location: ?semester=" . $semester_auto . "&week=" . $difference_weeks);
            } else {
                if (filter_input(INPUT_GET, "semester") && filter_input(INPUT_GET, "week")) {
                    $semester = htmlspecialchars(filter_input(INPUT_GET, "semester"));
                    $week = htmlspecialchars(filter_input(INPUT_GET, "week"));
                    $date_current_week_monday = $date_first_monday->modify("+" . $week - 1 . " weeks");
                    $date_weekday_iterator = clone $date_current_week_monday;
                    ?>
                    <div class="container-fluid">
                        <div class="d-flex mx-2 justify-content-between">
                            <a href="/schedule.php?semester=<?php echo $semester; ?>&week=<?php echo $week - 1 > 0 ? $week - 1 : 1; ?>"><h3>&lt;</h3></a>
                            <span><h3>Неделя <?php echo $week; ?></h3></span>
                            <a href="/schedule.php?semester=<?php echo $semester; ?>&week=<?php echo $week + 1; ?>"><h3>&gt;</h3></a>
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
                    <div class="container-fluid px-3 py-2">
                        <a href="https://ssau.ru/rasp?groupId=531233720&selectedWeek=<?php echo $week; ?>&selectedWeekday=1">ssau.ru расписание на эту неделю</a>
                        <p class="text-danger mb-0">Внимание! В настоящий момент расписание обновляется вручную! Смотрите актуальное расписание на сайте университета, а здесь записи занятий.</p>
                        <p class="text-muted mb-0">Выберите предмет, чтобы получить информацию</p>
                        <?php if (isLoggedIn() && $_SESSION["user_from_group"] === 1): ?>
                            <h2 class="mt-2">Описание</h2>
                            <?php if ($_SESSION["user_admin_rank"] === 1): ?>
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
                                                            <label for="hwOnThisDayInput">Домашнее задание на этот день</label>
                                                            <input type="text" id="hwOnThisDayInput" name="hwOnThisDayInput">
                                                        </div>
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
                            <div id="sendMediaInfo"></div>
                            <?php if ($_SESSION["user_admin_rank"] === 1): ?>
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
                            <?php endif; ?>
                            <div id="media" class="my-2"></div>
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
