<?php
    $channels = $_GET['channels'];
    $path = $_GET['path'];
    require 'functions.php';

    ini_set('memory_limit', '1000M');
    echo json_encode(getChannelData($path, $channels));
?>