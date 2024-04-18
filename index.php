<?php
require_once 'pdo.php';
require_once 'util.php';

session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Artem Shikhov's Resume Registry a1223bc2</title>
<?php require_once 'bootstrap.php'; ?>
</head>
<body>
<div class="container">
<h2>Artem Shikhov's Resume Registry</h2>
<?php
if ( !isset($_SESSION['user_id']) ) {
    echo '<p><a href="login.php">Please log in</a></p>';
}

flash();

$stmt = $pdo->query('SELECT * FROM Profile');
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {

    echo '<table border="1">';
    echo '<tr><td>';
    echo '<b>Name</b>';
    echo '</td><td>';
    echo '<b>Headline</b>';
    echo '</td><td>';
    echo '<b>Action</b>';
    echo '</td></tr>';

    while ( $row  ) {
        $fln = htmlentities($row['first_name']).' '.htmlentities($row['last_name']);
        $em = htmlentities($row['email']);
        $he = htmlentities($row['headline']);
        $su = htmlentities($row['summary']);
        $pid = htmlentities($row['profile_id']);
        echo '<tr><td>';
        echo '<a href="view.php?profile_id='.$pid.'">'.$fln.'</a>';
        echo '</td><td>';
        echo $he;
        echo '</td>';
        if ( isset($_SESSION['user_id']) ) {
            echo '<td>';
            echo '<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>';
            echo ' ';
            echo '<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>';
            echo '</td>';
        }
        echo '</tr>';
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    echo "</table><br>";
}

if ( isset($_SESSION['user_id']) ) {
    echo '<a href="logout.php">Logout</a></p>';
    echo '<p><a href="add.php">Add New Entry</a><br>';
}
?>
</div>
</body>
