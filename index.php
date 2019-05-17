<?php require 'php/common/head.php'; ?>

<h1>Главная</h1>

<?php
    require 'php/chart/functions.php';
    $path = 'data/2018/display.dat';
    $displays = getDisplays($path);
    
    echo $displays[1]->channels[0], '<br>';
    echo gettype($displays[1]->channels[0]);
?>

<?php require 'php/common/foot.php'; ?>