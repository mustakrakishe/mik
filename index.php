<?php require 'php/common/head.php'; ?>
<script src='js/libraries/jquery-3.4.1.min.js'></script>

<h1>Главная</h1>

<?php
    //header('location: chart.php');
    $path = 'D:/documents/task/task2018_web_interface/page/current/data';

    function getTechZones($path){
        if (file_exists($path)){
            return '<script>console.log("Файл ' . $path . ' существует.")</script>';
        }
        else{
            return '<script>console.log("Файл ' . $path . ' не существует.")</script>';
        }
    }

    echo getTechZones($path)
?>



<?php require 'php/common/foot.php'; ?>