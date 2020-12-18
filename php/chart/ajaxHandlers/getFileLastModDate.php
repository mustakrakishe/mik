<?php
    $status = false;
    $data = null;

    $path = $_GET['path'];

    if(file_exists($path)){
        require '../functions.php';

        $status = true;
        $data = getFileLastModDate($path);
    }

    echo json_encode([
        'status' => $status,
        'data' => $data
    ]);
?>