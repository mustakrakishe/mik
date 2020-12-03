<?php require 'php/common/head.php'; ?>

<script src='js/libraries/jquery-3.4.1.min.js'></script>
<link rel="stylesheet" href="css/anychart/anychart-ui.min.css">
<link rel="stylesheet" href="css/chart.css">

<script src='js/libraries/anychart/ru-ru.js'></script>
<script src='js/libraries/anychart/anychart-core.min.js'></script>
<script src='js/libraries/anychart/anychart-stock.min.js'></script>
<script src='js/libraries/anychart/anychart-exports.min.js'></script>
<script src='js/libraries/anychart/anychart-base.min.js'></script>
<script src='js/libraries/anychart/anychart-ui.min.js'></script>


<script src='js/stream/main.js'></script>
<script src='js/stream/mikRegistratorFileParsers.js'></script>
<script src='js/stream/functions.js'></script>


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
<div id="tab-paths" class="content-wrap tab">
    <p>ddmm.arh</p><input type="text" name="ddmmArh_path" id="ddmmArh_path" value="C:\Program Files (x86)\Microl\Mик-Регистратор\<?php echo date('dm') ?>.arh">
    <p>display.dat</p><input type="text" name="displayDat_path" id="displayDat_path" value="C:\Program Files (x86)\Microl\Mик-Регистратор\display.dat">
    <p>chanel.bas</p><input type="text" name="chanelBas_path" id="chanelBas_path" value="C:\Program Files (x86)\Microl\Mик-Регистратор\chanel.bas">
    <p>Отображаемый интервал</p><input type="text" name="displayedInterval" id="displayedInterval" value="60">
    <button id="streamButton" onclick="">Остановить стрим</button>
</div>

<div id="side-bar">
    <div id='shortcut-channels' class='shortcut'><p>Каналы</p></div>
    <div id='shortcut-paths' class='shortcut'><p>Пути</p></div>
</div>


<?php require 'php/common/foot.php'; ?>

