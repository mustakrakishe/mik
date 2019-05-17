<?php
    $date = $_GET['date'];
    $dateArr = explode('-', $date);
    $path = $_SERVER['DOCUMENT_ROOT'] . '/data/' . $dateArr[0] . '/chanel.bas';
    require 'functions.php';

    echo json_encode(getChannels($path));
?>