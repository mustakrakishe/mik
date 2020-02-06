<?php
    $path = $_GET['path'];
    $channels = $_GET['channels'];
    $timeB = $_GET['timeB'];
    $timeE = $_GET['timeE'];
    require 'functions.php';

    ini_set('memory_limit', '1000M');
    echo json_encode(parseArhFile($path, $channelIds, $timeB, $timeE));
?>