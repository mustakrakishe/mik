<?php require 'php/common/head.php'; ?>

<h1>Главная</h1>

<?php
$path = 'data/2018/1105.arh';
require 'php/chart/functions.php';
$channels = [0, 5, 20];
/*$data = getChannelData1($path, $channels);

    for ($pointNum = 0; $pointNum < 20; $pointNum++) {
        echo date('Y-m-d H:i:s', $data[0][$pointNum]);
        foreach($channels as $key => $channel){
            echo ' ', $data[$key+1][$pointNum];
        }
        echo '<br>';
    }*/

//$data = getChannelData1($path, $channels);
//print_r($data[0]);
?>

<?php require 'php/common/foot.php'; ?>