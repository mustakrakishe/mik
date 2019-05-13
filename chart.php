<?php require 'php/common/head.php'; ?>
<link rel="stylesheet" href="css/chart.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="js/chart.js"></script>

<h1>График</h1>

<form action="" method="post">
    <input type="date" name="date" value="2018-05-11">
    <select name="display" id="display"></select>
    <input type="submit" name="build_btn" hidden="hidden">
</form>

<div id="chartRow">
    <div id="chart"></div>
    <select name="channels" id="channels"></select>
</div>

<?php require 'php/common/foot.php'; ?>