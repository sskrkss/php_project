<?php
require_once 'pdo.php';
require_once 'util.php';

session_start();

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    unset($_SESSION['user_id']);
    if ( strlen($_POST['email']) > 0 && strlen($_POST['pass']) > 0 ) {
        if ( str_contains($_POST['email'], '@') ) {
            $check = hash('md5',  'XyZzy12*_'.$_POST['pass']);
            $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :em AND password = :pw');
            $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $row !== false ) {
                $_SESSION['user_id'] = $row['user_id'];
                error_log('Login success '.$_POST['email']);
                header('Location: index.php');
                return;
            } else {
                $_SESSION['failure'] = 'Incorrect email or password';
                error_log('Login fail '.$_POST['email']." $check");
                header('Location: login.php');
                return;
            }
        } else {
            $_SESSION['failure'] = 'Email must have an at-sign (@)';
            header('Location: login.php');
            return;
        }
    } else {
        $_SESSION['failure'] = 'User name and password are required';
        header('Location: login.php');
        return;
    }
} elseif ( isset($_POST['cancel'] ) ) {
    header('Location: index.php');
    return;
}
?>

<script type="text/javascript">
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
