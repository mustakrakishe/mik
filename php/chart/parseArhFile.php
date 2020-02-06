<?php
    $path = $_GET['path'];
    $channels = $_GET['channels'];
    $firstSecond = $_GET['firstSecond'];
    $lastSecond = $_GET['lastSecond'];
    require 'functions.php';

    ini_set('memory_limit', '1000M');
    echo json_encode(parseArhFile($path, $channels, $firstSecond, $lastSecond));
?>