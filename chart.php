<?php require 'php/common/head.php'; ?>
<link rel="stylesheet" href="css/chart.css">
<script src='js/jquery-3.4.1.min.js'></script>
<script src='js/chart.js'></script>

<script src='js/anychart/anychart-core.min.js'></script>
<script src='js/anychart/anychart-stock.min.js'></script>
<script src='js/anychart/anychart-exports.min.js'></script>
<script src='js/anychart/anychart-base.min.js'></script>

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
    <div id="shortcut-channels" class="shortcut"><p>Каналы</p></div>
</div>

<?php require 'php/common/foot.php'; ?>