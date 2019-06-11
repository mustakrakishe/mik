<?php require 'php/common/head.php'; ?>

<h1>Главная</h1>

<?php
    $path = 'data/2018/1105.arh';

    /*require 'php/chart/functions.php';
    $channels = [0];
    $data = getChannelData($path, $channels);
    for ($i = 0; $i < 20; $i++){
        echo implode(' ', $data[0][$i]), '<br>';
    }*/

    $date = strtotime(date('Y-m-d', filemtime($path)-1).' 00:00:00');
    echo date('Y-m-d H:i:s', $date);
    echo '<br>', $date;
?>

<?php require 'php/common/foot.php'; ?>