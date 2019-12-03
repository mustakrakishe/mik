<?php require 'php/common/head.php'; ?>
<link rel="stylesheet" href="css/chart.css">
<script src='js/libraries/jquery-3.4.1.min.js'></script>
<script src='js/chart.js'></script>

<script src='js/libraries/anychart/ru-ru.js'></script>
<script src='js/libraries/anychart/anychart-core.min.js'></script>
<script src='js/libraries/anychart/anychart-stock.min.js'></script>
<script src='js/libraries/anychart/anychart-exports.min.js'></script>
<script src='js/libraries/anychart/anychart-base.min.js'></script>
<script src='js/libraries/anychart/anychart-ui.min.js'></script>

<link rel="stylesheet" href="css/anychart/anychart-ui.min.css">

<script>
    anychart.format.inputLocale('ru-ru');
    anychart.format.outputLocale('ru-ru');
</script>

<div id="mainContent-wrap" class="content-wrap">
    <div id="controlRow">
        <form action="" method="post">
            <input type="date" name="date" value="2018-05-11">
            <select id="display" name="display"></select>
            <input type="submit" name="build_btn" hidden="hidden">
        </form>
    </div>
        
    <div id="chart">
        
    </div>
</div>


<div id="tab-channels" class="content-wrap tab">
    <select id="tab-channels-list" multiple id="tab-channels-list" name="channels"></select>
</div>

<div id="side-bar">
    <div id='shortcut-channels' class='shortcut'><p>Каналы</p></div>
</div>

<?php require 'php/common/foot.php'; ?>