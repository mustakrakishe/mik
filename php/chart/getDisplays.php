<?php
    $date = $_GET['date'];
    $dateArr = explode('-', $date);
    $path = $_SERVER['DOCUMENT_ROOT'] . '/data/' . $dateArr[0] . '/display.dat';
    require 'functions.php';

    if(file_exist($path)){
        echo json_encode(getDisplays($path));
    }
    else{
        echo ('Ошибка загрузки списка дисплеев. Отсутствует файл ' . $path . '.');
    }
?>