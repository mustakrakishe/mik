<?php
    $status = false;
    $data = null;

    if(file_exists($path = $_GET['path'])){
        $channels = $_GET['channels'];
        $firstSecond = $_GET['firstSecond'];
        $lastSecond = $_GET['lastSecond'];
        require '../functions.php';

        ini_set('memory_limit', '1000M');
        $status = true;
        $data = parseArhFile($path, $channels, $firstSecond, $lastSecond);
    }

    echo json_encode([
        'status' => $status,
        'data' => $data
    ]);

?>