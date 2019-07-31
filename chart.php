<?php require 'php/common/head.php'; ?>
<link rel="stylesheet" href="css/chart.css">
<script src='js/jquery-3.3.1.min.js'></script>
<script src='js/chart.js'></script>

<script src='js/anychart/anychart-core.min.js'></script>
<script src='js/anychart/anychart-stock.min.js'></script>
<script src='js/anychart/anychart-exports.min.js'></script>
<script src='js/anychart/anychart-base.min.js'></script>

<div id="controlRow">
    <form action="" method="post">
        <input type="date" name="date" value="2018-05-11">
        <select name="display"></select>
        <input type="submit" name="build_btn" hidden="hidden">
    </form>
</div>

<div id="chartRow">
    <div id="chart"></div>
    <div id="tab-channels">
        <div id="tab-channels-tongue"></div>
        <select id="tab-channels-list" multiple id="tab-channels-list" name="channels"></select>
    </div>
</div>

<?php require 'php/common/foot.php'; ?>