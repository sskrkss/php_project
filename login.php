<?php
    // устанавливаем соединение pdo, подключаем нашу библиотеку с вспомогательными функциями
    require_once 'pdo.php';
    require_once 'util.php';

    // начало сессии
    session_start();

    // кнопка log in
    if ( isset($_POST['email']) && isset($_POST['pass']) ) {

        // сбрасываем предыдущую сессию
        unset($_SESSION['user_id']);

        // проверка логина и пароля
        $check_login = login($pdo);

        // если не ок, редирект с уведомлением об ошибке
        if ( $check_login === false ) {
            header('Location: login.php');
            return;
        }

        // если все ок, сохраняем в сессию id, редирект на главную
        $_SESSION['user_id'] = $check_login;
        header('Location: index.php');

    // кнопка отменить
    } elseif ( isset($_POST['cancel'] ) ) {
        header('Location: index.php');
        return;
    }
?>

<script type="text/javascript">
    // валидация на уровне клиента
    function doValidate() {
        console.log('Validating...');
        try {
            addr = document.getElementById('email').value;
            pw = document.getElementById('id_1723').value;
            console.log("Validating addr="+addr);
            console.log("Validating pw="+pw);
            if (addr == null || addr == "" || pw == null || pw == "") {
                alert('Both fields must be filled out');
                return false;
            }
            if (addr.includes('@') === false) {
                alert('Email must have an at-sign (@)');
                return false;
            }
            return true;
        } catch(e) {
            return false;
        }
        return false;
    }
</script>

<!DOCTYPE html>
<html>
    <head>
        <?php require_once 'bootstrap.php'; ?>
        <title>Artem Shikhov's Login Page a1223bc2</title>
    </head>
    <body>
        <div class="container">
            <h2>Please Log In</h2>
            <?php flash(); ?>
            <form method="POST">
                <label for="email">User Name</label>
                <input type="text" name="email" id="email"><br/>
                <label for="id_1723">Password</label>
                <input type="password" name="pass" id="id_1723"><br/>
                <input type="submit" onclick="return doValidate();" value="Log In">
                <input type="submit" name="cancel" value="Cancel">
            </form>
        </div>
    </body>
</html>