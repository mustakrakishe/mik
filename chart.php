<?php require 'head.php'; ?>
<link rel="stylesheet" href="css/chart.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<h1>График канала 1</h1>

<?php
    function getValue($varName, $defaultValue){
        if (isset($_POST[$varName])){
            return ($_POST[$varName]);
        }
        else{
            return ($defaultValue);
        }
    }

    $date = getValue('date', '2018-05-11'); //date('Y-m-d', mktime())
    $timeB = date('H:i:s', strtotime(getValue('timeB', '00:00:00')));
    $timeE = date('H:i:s', strtotime(getValue('timeE', '00:10:00')));
    $channel = getValue('channel', '1');
?>

<form action="" method="post">
    <input type="date" name="date" value="<?php echo $date; ?>">
    <input type="time" name="timeB" step="1" value="<?php echo $timeB; ?>">
    <input type="time" name="timeE" step="1" value="<?php echo $timeE; ?>">
    <input type="text" name="channel" value="<?php echo $channel; ?>">
    <input type="submit" name="build_btn" hidden="hidden">
</form>

<div id="charts">
    <div id="сhart1" class="chart"></div>
</div>

<?php
    require 'functions.php';
    if ($archPoints = getArchPoints($date, $timeB, $timeE, $channel)){
        echo 'timeB: ', $timeB, ' timeE: ', $timeE, ';<br>';
        echo end($archPoints)[0], '<br>';
        print_r ($phpPoints = array_merge([['time', 'Channel 1']], getInstantPoints($archPoints, 5)));
    }
    else{
        echo 'Файл не найден.';
    }
?>

<script>
    var jsPoints = <?php echo json_encode($phpPoints); ?>;
    var pointsCount = jsPoints.length-1;
    for(var i = 1; i <= pointsCount; i++){
        jsPoints[i][1] = parseFloat(jsPoints[i][1]);
    }

    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var data = google.visualization.arrayToDataTable(jsPoints);
        var options = {
            title: 'Производный',
            curveType: 'none',
            legend: { position: 'bottom' }
        };
        var chart = new google.visualization.LineChart(document.getElementById('сhart1'));
        chart.draw(data, options);
    }
</script>

<?php require 'foot.php'; ?>