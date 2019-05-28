<?php require 'php/common/head.php'; ?>

<h1>Главная</h1>


<script src="js/anychart/anychart-base.min.js"></script>
<script src="js/anychart/anychart-ui.min.js"></script>
<script src="js/anychart/anychart-exports.min.js"></script>

<?php require 'php/chart/init.php'; ?>
<?php require 'php/chart/getChannelData.php'; ?>

<?php
    $path = 'data/2018/1105.arh';
    $channels = [0, 1, 2, 3, 4, 5, 6, 7];

    $channelData =  getChannelData($path, $channels);
    foreach($channelData as $key => $channel){
        echo '<br>', 'КАНАЛ ', $key, '<br>';
        foreach($channel as $point){
            echo $point[0], ' ', $point[1], '<br>';
        }
    }
?>

<?php require 'php/common/foot.php'; ?>