<?php 

// полностью уничтожаем сессию и содержимое массива
session_start();
session_destroy();
header('Location: index.php');