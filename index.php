<?php require 'php/common/head.php'; ?>
<script src='js/libraries/jquery-3.4.1.min.js'></script>

<h1>Главная</h1>

<?php
    //header('location: chart.php');
    $path = 'D:\documents\task\task2018_web_interface\page\current\data\O11\2018-05-11\chanel.bas';
    $channels = [];

    class channel{
        public $name = '';
        public $units = '';
        public $scaleL = '';
        public $scaleH = '';

        function __construct($name, $units, $scaleL, $scaleH){
            $this->name = $name;
            $this->units = $units;
            $this->scaleL = $scaleL;
            $this->scaleH = $scaleH;
        }
    }
    $handle = fopen($path, 'r');

    while(!feof($handle)){
        echo $string = iconv("windows-1251","utf-8", fgets($handle)).'<br>';

        if((int)substr($string, strpos($string, '<') + 1, 1)){
            //list($name, , $units, $scaleL, $scaleH, , $arch) = preg_split('@[\\\/(,)<#]@', $string);
            //array_push($channels, new channel($name, $units, $scaleL, $scaleH));

            $data = [];
            $separators = ['\\', '/', '(', ',', ')', '<', '#'];
            $firstCharPos = 0;
            foreach($separators as $separator){
                $lastCharPos = strpos($string, $separator);
                $sliceLength = $lastCharPos - $firstCharPos;
                array_push($data, substr($string, $firstCharPos, $sliceLength));
                $firstCharPos = $lastCharPos + 1;
            }
            list($name, , $units, $scaleL, $scaleH) = $data;
            echo 'Имя канала: '.$name.'<br>';
            echo 'Единицы измерения: '.$units.'<br>';
            echo 'Нижний предел: '.$scaleL.'<br>';
            echo 'Верхний предел: '.$scaleH.'<br>';
        }
    }

    fclose($handle);
?>

<?php require 'php/common/foot.php'; ?>