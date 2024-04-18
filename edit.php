<?php
    // устанавливаем соединение pdo, подключаем нашу библиотеку с вспомогательными функциями
    require_once 'pdo.php';
    require_once 'util.php';

    // начало сессии, проверка авторизации, проверка наличия profile_id в БД, достаем данные из Profile
    session_start();
    login_check();
    $profile = get_profile($pdo);

    // кнопка сохранить
    if ( isset($_POST['save']) ) {

        // валидируем входящие данные
        $check_profile = profile_validation();
        $check_position = position_validation();
        $check_education = education_validation();

        // если не ок, редирект с уведомлением об ошибке
        if ( $check_profile === false || $check_position === false || $check_education === false ) {
            header('Location: add.php');
            return;
        }

        // обновляем данные в БД
        update_profile($pdo);
        update_position($pdo);
        update_education($pdo);

        $_SESSION['success'] = 'Profile updated';
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
        <title>Artem Shikhov's Profile Edit a1223bc2</title>
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
            <h2>Editing Profile for UMSI</h2>
            <?php flash(); ?>
            <form method="post">
                <p>First Name:
                    <input type="text" name="first_name" size="60" value="<?= $profile['first_name'] ?>"/></p>
                <p>Last Name:
                    <input type="text" name="last_name" size="60" value="<?= $profile['last_name'] ?>"/></p>
                <p>Email:
                    <input type="text" name="email" size="30" value="<?= $profile['email'] ?>"/></p>
                <p>Headline:
                    <br/>
                    <input type="text" name="headline" size="80" value="<?= $profile['headline'] ?>"/></p>
                <p>Summary:
                    <br/>
                    <textarea name="summary" rows="8" cols="80"><?= $profile['summary']  ?></textarea></p>
                <p>Education:
                    <input type="submit" id="addEdu" value="+"/></p>
                <p>
                    <div id="education_fields">
                        <?php
                            $countEdu = 0;
                            $stmt = $pdo->prepare('SELECT * 
                                                    FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id 
                                                    WHERE profile_id = :pid ORDER BY `rank`');
                            $stmt->execute(array(':pid' => $_GET['profile_id']));
                            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                                $countEdu++;
                                echo '<div id="education'.$countEdu.'">';
                                echo '<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.htmlentities($row['year']).'"/>';
                                echo '<input type="button" value="-" onclick="$(\'#education'.$countEdu.'\').remove();return false;"/></p>';
                                echo '<p>School: <input type="text" size="80" name="edu_school'.$countEdu.'" class="school" value="'.htmlentities($row['name']).'" /></p>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                    <script type="text/javascript">
                        countEdu = <?= $countEdu ?>;
                        $('#addEdu').click(function(event){
                            event.preventDefault();
                            if ( countEdu >= 9 ) {
                                alert("Maximum of nine education entries exceeded");
                                return;
                            }
                            countEdu++;
                            window.console && console.log("Adding education "+countEdu);
                        
                            $('#education_fields').append(
                                '<div id="edu'+countEdu+'"> \
                                <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
                                <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
                                <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
                                </p></div>'
                            );
                        
                            $('.school').autocomplete({
                                source: "school.php"
                            });
    
                        });
                    </script>
                </p>
                <p>Position:
                    <input type="submit" id="addPos" value="+"/></p>
                <p>
                    <div id="position_fields">
                        <?php
                            $countPos = 0;
                            $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :pid');
                            $stmt->execute(array(':pid' => $_GET['profile_id']));
                            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                                $countPos++;
                                echo '<div id="position'.$countPos.'">';
                                echo '<p>Year: <input type="text" name="year'.$countPos.'" value="'.htmlentities($row['year']).'"/>';
                                echo '<input type="button" value="-" onclick="$(\'#position'.$countPos.'\').remove();return false;"/></p>';
                                echo '<textarea name="desc'.$countPos.'" rows="8" cols="80">'.htmlentities($row['description']).'</textarea>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                    <script type="text/javascript">
                        countPos = <?= $countPos ?>;
                        $(document).ready(function(){
                            window.console && console.log('Document ready called');
                            $('#addPos').click(function(event){
                                event.preventDefault();
                                if ( countPos >= 9 ) {
                                    alert("Maximum of nine position entries exceeded");
                                    return;
                                }
                                countPos++;
                                window.console && console.log("Adding position "+countPos);
                                $('#position_fields').append(
                                    '<div id="position'+countPos+'"> \
                                    <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                                    <input type="button" value="-" \
                                        onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                                    <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                                    </div>');
                            });
                        });
                    </script>
                    <input type="submit" name="save" value="Save">
                    <input type="submit" name="cancel" value="Cancel">
                </p>
            </form>
        </div>
    </body>
</html>