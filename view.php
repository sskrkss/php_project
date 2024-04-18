<?php
    // устанавливаем соединение pdo, подключаем нашу библиотеку с вспомогательными функциями
    require_once 'pdo.php';
    require_once 'util.php';

    // начало сессии, проверка наличия profile_id в БД, достаем данные из Profile
    session_start();
    $profile = get_profile($pdo);

    // если не ок, редирект с уведомлением об ошибке
    if ( $profile === false ) {
        header('Location: index.php');
        return;
    }    
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Artem Shikhov's Profile View a1223bc2</title>
        <?php require_once 'bootstrap.php'; ?>
    </head>
    <body>
        <div class="container">
            <h2>Profile information</h2>
            <p>First Name: <?= $profile['first_name'] ?></p>
            <p>Last Name: <?= $profile['last_name'] ?></p>
            <p>Email: <?= $profile['email'] ?></p>
            <p>Headline:<br/><?= $profile['headline'] ?></p>
            <p>Summary:<br/><?= $profile['summary'] ?></p>
            <p>Education:</p>
                <ul>
                    <?php
                        $stmt = $pdo->prepare('SELECT * 
                                                FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id 
                                                WHERE profile_id = :pid ORDER BY `rank`' 
                                            );
                        $stmt->execute(array(':pid' => $_GET['profile_id']));
                        while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                            $res = htmlentities($row['year']).': '.htmlentities($row['name']);
                            echo '<li>'.$res.'</li>';
                        }
                    ?>
                </ul>
            <p>Position:</p>
                <ul>
                    <?php
                        $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :pid');
                        $stmt->execute(array(':pid' => $_GET['profile_id']));
                        while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                            $res = htmlentities($row['year']).': '.htmlentities($row['description']);
                            echo '<li>'.$res.'</li>';
                        }
                    ?>
                </ul>
            <a href="index.php">Done</a>
        </div>
    </body>
</html>