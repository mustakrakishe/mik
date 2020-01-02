<?php
    $path = $_GET['path'];
    require 'functions.php';
    $path = 'D:/documents/task/task2018_web_interface/page/current/data/O11/2018-05-11/chanel.bas';

    echo json_encode(getChannels($path));
?>