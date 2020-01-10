<?php require 'php/common/head.php'; ?>

<script src='js/libraries/jquery-3.4.1.min.js'></script>
<link rel="stylesheet" href="css/anychart/anychart-ui.min.css">
<link rel="stylesheet" href="css/chart.css">

<script src='js/chart/functions.js'></script>
<script src='js/chart/main.js'></script>

<script src='js/libraries/anychart/ru-ru.js'></script>
<script src='js/libraries/anychart/anychart-core.min.js'></script>
<script src='js/libraries/anychart/anychart-stock.min.js'></script>
<script src='js/libraries/anychart/anychart-exports.min.js'></script>
<script src='js/libraries/anychart/anychart-base.min.js'></script>
<script src='js/libraries/anychart/anychart-ui.min.js'></script>


<script>
    anychart.format.inputLocale('ru-ru');
    anychart.format.outputLocale('ru-ru');
</script>

<div id="mainContent-wrap" class="content-wrap">
    <div id="controlRow">
        <form action="" method="post">
            <input id="date" type="date" name="date" value="2018-05-11">
            <select id="display" name="display"></select>
            <input type="submit" name="build_btn" hidden="hidden">
        </form>
    </div>
        
    <div id="chart"></div>
</div>

<?php
    $path = "./data/";
    $techZones = [];

    if($handle = opendir($path)){
        while($entry = readdir($handle)){
            if(($entry != '.') && ($entry != '..')){
                array_push($techZones, $entry);
            }
        }
        closedir($handle);
    }
?>

<div id="tab-techZones" class="content-wrap tab">
    <?php
        foreach($techZones as $techZoneName){
            echo '<label><input type="radio" name="techZone" value="' . $techZoneName . '">' . $techZoneName . '</label>';
        }
    ?>
</div>
<div id="tab-channels" class="content-wrap tab">
    <select id="tab-channels-list" multiple id="tab-channels-list" name="channels"></select>
</div>

<div id="side-bar">
    <div id='shortcut-techZones' class='shortcut'><p>Тех. зоны</p></div>
    <div id='shortcut-channels' class='shortcut'><p>Каналы</p></div>
</div>



<?php require 'php/common/foot.php'; ?>