<?php
    $path = $_GET['path'];
    require 'functions.php';

    echo json_encode(getFileLastModDate($path));
?>