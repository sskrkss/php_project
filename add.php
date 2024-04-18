<?php
    // устанавливаем соединение pdo, подключаем нашу библиотеку с вспомогательными функциями
    require_once 'pdo.php';
    require_once 'util.php';

    // начало сессии, проверка авторизации
    session_start();
    login_check();

    // кнопка добавить
    if ( isset($_POST['add']) ) {
        
        // валидируем входящие данные
        $check_profile = profile_validation();
        $check_position = position_validation();
        $check_education = education_validation();

        // если не ок, редирект с уведомлением об ошибке
        if ( $check_profile === false || $check_position === false || $check_education === false ) {
            header('Location: add.php');
            return;
        }

        // добавляем валидированные данные в БД
        insert_profile($pdo);
        insert_position($pdo, $_SESSION['profile_id']);
        insert_education($pdo, $_SESSION['profile_id']);


        $_SESSION['success'] = 'Profile added';
        header('Location: index.php');
        return;

    // кнопка отменить
    } elseif ( isset($_POST['cancel']) ) {
        header('Location: index.php');
        return;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Artem Shikhov's Profile Add a1223bc2</title>
        <?php require_once 'bootstrap.php'; ?>
        <link rel="stylesheet" 
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
            integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
            crossorigin="anonymous">
        <link rel="stylesheet" 
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
            integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
            crossorigin="anonymous">
        <link rel="stylesheet" 
            href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
        <script
          src="https://code.jquery.com/jquery-3.2.1.js"
          integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
          crossorigin="anonymous"></script>
        <script
          src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
          integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
          crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container">
            <h2>Adding Profile for UMSI</h2>
            <?php flash(); ?>
            <form method="post">
                <p>First Name:
                    <input type="text" name="first_name" size="60"/></p>
                <p>Last Name:
                    <input type="text" name="last_name" size="60"/></p>
                <p>Email:
                    <input type="text" name="email" size="30"/></p>
                <p>Headline:
                    <br/>
                    <input type="text" name="headline" size="80"/></p>
                <p>Summary:
                    <br/>
                    <textarea name="summary" rows="8" cols="80"></textarea></p>
                <p>Education:
                    <input type="submit" id="addEdu" value="+"/>
                    <div id="edu_fields"></div></p>
                <p>Position:
                    <input type="submit" id="addPos" value="+"/>
                    <div id="position_fields"></div></p>
                <script src="plusminus_buttons.js"></script>
                <p>
                    <input type="submit" name="add" value="Add">
                    <input type="submit" name="cancel" value="Cancel">
                </p>
            </form>
        </div>
    </body>
</html>