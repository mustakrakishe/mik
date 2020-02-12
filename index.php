<?php require 'php/common/head.php'; ?>
<script src='js/libraries/jquery-3.4.1.min.js'></script>

<h1>Главная</h1>

<?php
    //header('location: chart.php');

    require 'php/chart/functions.php';
    $path = 'C:/Program Files (x86)/Microl/Mик-Регистратор/0702.arh';
    $data = parseArhFile($path, [1], 0, 86400);
    echo count($data) + ' точек';
?>

<?php require 'php/common/foot.php'; ?>