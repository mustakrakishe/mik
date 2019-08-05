<?php require 'php/common/head.php'; ?>
<link rel="stylesheet" href="css/chart.css">
<link rel="stylesheet" href="css/font-awesome-4.7.0/css/font-awesome.min.css">
<script src='js/jquery-3.3.1.min.js'></script>
<script src='js/chart.js'></script>

<script src='js/anychart/anychart-core.min.js'></script>
<script src='js/anychart/anychart-stock.min.js'></script>
<script src='js/anychart/anychart-exports.min.js'></script>
<script src='js/anychart/anychart-base.min.js'></script>

<div id="mainContent-wrap" class="content-wrap">
    <div id="controlRow" class="content-header">
        <form action="" method="post">
            <input type="date" name="date" value="2018-05-11">
            <select id="display" name="display"></select>
            <input type="submit" name="build_btn" hidden="hidden">
        </form>
    </div>

    <div id="chartRow" class="content">
        <div id="chart"></div>
    </div>
</div>

<div id="tab-channels" class="content-wrap tab">
    <div id="tab-channels-header" class="content-header">Каналы</div>
    <i class="fa fa-times icon-close-window" aria-hidden="true"></i>
    <select id="tab-channels-list" class="content" multiple id="tab-channels-list" name="channels"></select>
</div>

<div id="side-bar">
    <div id="shortcut-channels" class="shortcut"><p>Каналы</p></div>
</div>

<?php require 'php/common/foot.php'; ?>