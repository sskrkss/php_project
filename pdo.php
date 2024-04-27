<?php

// устанавливаем соединение pdo
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'pZo922av9.KS5!tu');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);