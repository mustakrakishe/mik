<?php require 'php/common/head.php'; ?>
<link rel="stylesheet" href="css/chart.css">
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

<?php
    //$path = 'data/2018/display.dat';
?>

<?php require 'php/common/foot.php'; ?>