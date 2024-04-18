<?php
require_once 'pdo.php';
require_once 'util.php';

session_start();
login_check();
$profile = get_profile($pdo);

if ( isset($_POST['delete']) && $profile !== false ) {
    $sql = "DELETE FROM Profile WHERE profile_id = :pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
} elseif ( isset($_POST['cancel']) || $profile === false) {
    header('Location: index.php');
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Artem Shikhov's Profile Delete a1223bc2</title>
<?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container">
<h2>Deleting Profile</h2>
<p>First Name: <?= $profile['first_name'] ?></p>
<p>Last Name: <?= $profile['last_name'] ?></p>
<form method="post">
<input type="submit" value="Delete" name="delete">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>
</html>