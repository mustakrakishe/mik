<?php require 'php/common/head.php'; ?>
<script src='js/libraries/jquery-3.4.1.min.js'></script>

<h1>Главная</h1>

<?php
    header('location: chart.php');

    // include('php\chart\functions.php');
    // $path = 'C:/Program Files (x86)/Microl/Mик-Регистратор/0212.arh';
    // $channels = [19, 27];
    // $firstSecond = 0;
    // $lastSecond = 86400;

    // ini_set('memory_limit', '1000M');
    
    // $data = parseArhFile($path, $channels, $firstSecond, $lastSecond);
    // var_dump($data);
?>

<?php require 'php/common/foot.php'; ?>