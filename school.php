<?php

require_once 'pdo.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
$stmt->execute(array(':prefix' => $_REQUEST['term']."%"));
$rows = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
$rows[] = $row['name'];
}

echo(json_encode($rows, JSON_PRETTY_PRINT));