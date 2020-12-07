<?php require 'php/common/head.php'; ?>

<script src='js/libraries/jquery-3.4.1.min.js'></script>
<link rel="stylesheet" href="css/anychart/anychart-ui.min.css">
<link rel="stylesheet" href="css/chart.css">

<script src='js/chart/main.js'></script>
<script src='js/chart/mikRegistratorFileParsers.js'></script>
<script src='js/chart/functions.js'></script>

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
            <select id="techZone" name="techZone" class="controlItem">
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

                <?php
                    foreach($techZones as $techZoneName){
                        echo '<option value="' . $techZoneName . '">' . $techZoneName . '</label>';
                    }
                ?>
            </select>
            <select id="display" name="display" class="controlItem"></select>
            <input id="date" type="date" name="date" value="2018-05-11" class="controlItem">
            <input type="submit" name="build_btn" hidden="hidden">
        </form>
    </div>
        
    <div id="chart"></div>
</div>


<div id="tab-channels" class="content-wrap tab">
    <select id="channels" name="channels" class="controlItem" multiple></select>
</div>

<div id="side-bar">
    <div id='shortcut-channels' class='shortcut'><p>Каналы</p></div>
</div>



<?php require 'php/common/foot.php'; ?>