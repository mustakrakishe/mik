<?php require 'php/common/head.php'; ?>
<script src='js/jquery-3.3.1.min.js'></script>

<h1>Главная</h1>

<?php
$path = 'data/2018/1105.arh';
require 'php/chart/functions.php';
$channels = [0, 5, 20];
/*$data = getChannelData1($path, $channels);

    for ($pointNum = 0; $pointNum < 20; $pointNum++) {
        echo date('Y-m-d H:i:s', $data[0][$pointNum]);
        foreach($channels as $key => $channel){
            echo ' ', $data[$key+1][$pointNum];
        }
        echo '<br>';
    }*/

//$data = getChannelData1($path, $channels);
//print_r($data[0]);
?>

<style>
    .slidingDiv {
        height:300px;
        width: 300px;
        background-color: #99CCFF;
        border-bottom:5px solid #3399FF;
    }
    .show_hide {
        display:none;
    }
</style>

<script>
    $(document).ready(function () {
        $(".slidingDiv").hide();
        $(".show_hide").show();

        $('.show_hide').click(function () {
            $(".slidingDiv").slideUp(300);
        });
    });
</script>

<div class="slidingDiv">
    <p>Fill this space with really interesting content.</p>
    <a href="#" class="show_hide">hide</a>
</div>
<a href="#" class="show_hide">Show/hide</a>



<?php require 'php/common/foot.php'; ?>