<?php require 'php/common/head.php'; ?>

<h1>Главная</h1>

<?php
    $arr = [0, -2, 3, -18, 25, 30];

    $arr_positive = array_filter($arr, function($num){
            return $num>=0;
    });

    print_r($arr_positive);
?>

<?php require 'php/common/foot.php'; ?>