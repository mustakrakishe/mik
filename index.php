<?php require 'php/common/head.php'; ?>
<script src='js/libraries/jquery-3.4.1.min.js'></script>

<h1>Главная</h1>

<?php
    //header('location: chart.php');

    require 'php/chart/functions.php';
    $path = 'D:/documents/task/task2018_web_interface/page/current/data/O11/2018-05-11/2018/1105.arh';
    $chanelIds = [0, 1, 3];
    $firstSec = 0;
    $lastSec = 15;
    $data = parseArhFile($path, $chanelIds, $firstSec, $lastSec);
    print_r($data);
?>

<?php require 'php/common/foot.php'; ?>