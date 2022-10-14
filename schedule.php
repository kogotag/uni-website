<?php require_once 'php/config.inc.php'; ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>1105 Сайт</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <?php require 'navbar.php'; ?>
        <?php
        try {
            $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
            if (!filter_input_array(INPUT_GET)) {


                header("Location: ?type=table&semester=1&week=7"); // TODO: current week
            } else {
                if (filter_input(INPUT_GET, "type") && filter_input(INPUT_GET, "type") === "table") {
                    if (filter_input(INPUT_GET, "semester") && filter_input(INPUT_GET, "week")) {
                        $semester = htmlspecialchars(filter_input(INPUT_GET, "semester"));
                        $week = htmlspecialchars(filter_input(INPUT_GET, "week"));
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Понедельник</th>
                                        <th>Вторник</th>
                                        <th>Среда</th>
                                        <th>Четверг</th>
                                        <th>Пятница</th>
                                        <th>Суббота</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    for ($number = 1; $number <= 6; $number++) {
                                        echo '<tr>';
                                        $stmt_row = $dbh->prepare("SELECT * FROM `ssau_schedule` WHERE semester=? AND week=? AND number=?;");
                                        $result_row = $stmt_row->execute(array($semester, $week, $number));
                                        $subjects = [];
                                        if ($result_row) {
                                            $subjects = $stmt_row->fetchAll();
                                        }
                                        foreach ($subjects as $subject) {
                                            echo '<td id="selectableCell">';
                                            echo $subject['subject_name'], '<br>', $subject['subject_lecturer'], '<br>', $subject['subject_classroom'];
                                            echo '</td>';
                                        }
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    } else {
                        echo 'semester or week not specified';
                    }
                } elseif (filter_input(INPUT_GET, "type") && filter_input(INPUT_GET, "type") === "subject") {
                    
                } else {
                    echo 'Invalid type';
                }
            }
        } catch (Exception $ex) {
            print "Error!: " . $ex->getMessage() . "<br>";
            die();
        }
        ?>

        <span class="text-left text-info lead">Пока ещё не готово, но скоро будет :)</span>
        <script src="js/mainscript.js"></script>
        <script src="js/schedule_table.js"></script>
        <script src="js/jquery-3.6.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
