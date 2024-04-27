<?php

// устанавливаем соединение pdo
require_once 'pdo.php';

// начинаем сессию, указываем содиржимое файла (json)
session_start();
header('Content-Type: application/json; charset=utf-8');

// делаем запрос в БД по регулярному выражению: содержимое формы + любое количество символов
$stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
$stmt->execute(array(':prefix' => $_REQUEST['term']."%"));
$rows = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
$rows[] = $row['name'];
}

// кодируем полученный результат в json, выводим в файл
echo(json_encode($rows, JSON_PRETTY_PRINT));