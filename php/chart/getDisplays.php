<?php
    $date = $_GET['date'];
    require 'functions.php';

    echo json_encode(getDisplays($date));
?>