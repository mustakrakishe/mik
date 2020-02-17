<?php require 'php/common/head.php'; ?>
<script src='js/libraries/jquery-3.4.1.min.js'></script>

<h1>Главная</h1>

<?php
    //header('location: chart.php');

    require 'php/chart/functions.php';
    $path = 'C:/Program Files (x86)/Microl/Mик-Регистратор/1702.arh';
    $data = parseArhFile($path, [1], 0, 86400);
    echo $data[0][0] . ' ' . $data[0][1] . '<br>';
    echo date('Y-m-d H:i:s', $data[0][0]/1000);
?>

<script src='js/chart/functions.js'></script>
<script>
    var phpTime = <?php echo  json_encode($data[0][0])?>;
    console.log('phpTime = ' + phpTime);

    var path = 'C:/Program Files (x86)/Microl/Mик-Регистратор/1702.arh';
    var channels = [1];
    var firstSecond = 0;
    var lastSecond = 86400;
    
    parseArhFile(path, channels, firstSecond, lastSecond)
    .then(data => {
        console.log(data[0]);
        console.log(new Date(data[0][0]));
    })
</script>

<?php require 'php/common/foot.php'; ?>