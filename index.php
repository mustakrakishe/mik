<?php require 'php/common/head.php'; ?>

<h1>Главная</h1>

<?php
    require 'php/chart/functions.php';
    $date = '2018-05-11';
    $displays = getDisplays($date);

    echo 'Дисплеи:<br><br>';
    foreach($displays as $display){
        echo 'Номер: ', $display->num, '<br>';
        echo 'Имя: ', $display->name, '<br>';
        echo 'Каналы: ', implode('; ', $display->channels), '<br><br>';
    }
?>

<?php require 'php/common/foot.php'; ?>