<?php
    $channels = $_GET['channels'];
    $date = $_GET['date'];
    $dateArr = explode('-', $date);
    $path = $_SERVER['DOCUMENT_ROOT'] . '/data/' . $dateArr[0] . '/'. $dateArr[2] . $dateArr[1] . '.arh';
    require 'functions.php';

    ini_set('memory_limit', '1000M');
    echo json_encode(getChannelData($path, $channels));
?>