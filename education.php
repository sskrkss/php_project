<?php

require_once 'pdo.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->query('SELECT * FROM Educations');
$rows = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $rows[] = $row;
}

echo json_encode($rows, JSON_PRETTY_PRINT);