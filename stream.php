<?php require 'php/common/head.php'; ?>

<script src='js/libraries/jquery-3.4.1.min.js'></script>
<link rel="stylesheet" href="css/anychart/anychart-ui.min.css">
<link rel="stylesheet" href="css/chart.css">

<script src='js/libraries/anychart/ru-ru.js'></script>
<script src='js/libraries/anychart/anychart-core.min.js'></script>
<script src='js/libraries/anychart/anychart-stock.min.js'></script>
<script src='js/libraries/anychart/anychart-exports.min.js'></script>
<script src='js/libraries/anychart/anychart-base.min.js'></script>
<script src='js/libraries/anychart/anychart-ui.min.js'></script>


<script src='js/chart/functions.js'></script>


<script>
    anychart.format.inputLocale('ru-ru');
    anychart.format.outputLocale('ru-ru');
</script>

<div id="mainContent-wrap" class="content-wrap">
    <div id="controlRow">
        <form action="" method="post">
            <select id="techZone" name="techZone" class="controlItem">
                <?php
                    $path = "./data/";
                    $techZones = [];

                    if($handle = opendir($path)){
                        while($entry = readdir($handle)){
                            if(($entry != '.') && ($entry != '..')){
                                array_push($techZones, $entry);
                            }
                        }
                        closedir($handle);
                    }
                ?>

                <?php
                    foreach($techZones as $techZoneName){
                        echo '<option value="' . $techZoneName . '">' . $techZoneName . '</label>';
                    }
                ?>
            </select>
            <select id="display" name="display" class="controlItem"></select>
            <input id="date" type="date" name="date" value="2018-05-11" class="controlItem">
            <input type="submit" name="build_btn" hidden="hidden">
        </form>
    </div>
        
    <div id="chart"></div>
</div>


<div id="tab-channels" class="content-wrap tab">
    <select id="channels" name="channels" class="controlItem" multiple></select>
</div>
<div id="tab-paths" class="content-wrap tab">
    <p>ddmm.arh</p><input type="text" name="ddmmArh_path" id="ddmmArh_path" value="C:\Program Files (x86)\Microl\Mик-Регистратор\0302.arh">
    <p>display.dat</p><input type="text" name="displayDat_path" id="displayDat_path" value="C:\Program Files (x86)\Microl\Mик-Регистратор\display.dat">
    <p>chanel.bas</p><input type="text" name="chanelBas_path" id="chanelBas_path" value="C:\Program Files (x86)\Microl\Mик-Регистратор\chanel.bas">
    <button onclick="update()">Обновить</button>
</div>

<div id="side-bar">
    <div id='shortcut-channels' class='shortcut'><p>Каналы</p></div>
    <div id='shortcut-paths' class='shortcut'><p>Пути</p></div>
</div>


<script>
var plot;
    $(document).ready(function (){
        anychart.exports.server("http://localhost:2000");
        anychart.format.inputLocale('ru-ru');
        anychart.format.outputLocale('ru-ru');

        //create chart
        var chart = anychart.stock();
        plot = chart.plot();

        chart.container('chart');
        chart.scroller(false);
        chart.interactivity().zoomOnMouseWheel(true);
        chart.crosshair().xLabel(false);
        chart.crosshair().yLabel(false);
        chart.margin(-5, -25, -35, -15);

        chart.contextMenu().itemsFormatter(function(items){
            delete items["save-data-as"];
            delete items["share-with"];
            delete items["exporting-separator"];
            delete items["full-screen-enter"];
            delete items["full-screen-separator"];
            delete items["about"];
            return items;
        });
        
        
        plot.legend().fontColor('Black');
        plot.legend().position('bottom');
        plot.legend().itemsLayout("horizontal-expandable");
        plot.legend().padding().top(35);
        plot.legend().title(false);
        plot.crosshair().yStroke(null);

        plot.xMinorGrid(true);
        plot.xAxis().minorLabels().format('{%tickValue}{dateTimeFormat:HH:mm:ss}').fontColor('black');
        plot.xAxis().labels(false);
        plot.xAxis().background().enabled(true).stroke('none').fill('none');
        plot.xAxis().background().topStroke('1 black');
        chart.xScale().ticksCount(12);
        
        plot.yMinorGrid(true);
        plot.yAxis().labels().fontColor('black');
        plot.yAxis().ticks(false);
        plot.yAxis().stroke('black');
        plot.yAxis().labels(false);

        chart.draw();

        preloader = anychart.ui.preloader();
        preloader.render(document.getElementById("chart"));
        update(plot);

        
        /*Отобhажение боковой панели*/
        $('.shortcut').click(function(){
            var activatedShortcutId = this.getAttribute('id');
            var activatedTabId = activatedShortcutId.replace('shortcut-', 'tab-');

            var activatedShortcut = this;
            var activatedTab = $('#' + activatedTabId);

            //Если была неактивна
            if($(activatedTab).css('display') == 'none'){

                var openedTab = $('.tab').filter(function(){ 
                    return this.style.display == 'block';
                });

                if(openedTab.length){
                    var openedTabId = $(openedTab).attr('id');
                    var activeShortcutId = openedTabId.replace('tab-', 'shortcut-');
                    
                    var activeShortcut = $('#' + activeShortcutId);
                    $(openedTab).css('display', 'none');
                    activeShortcut.css('background-color', '');
                }

                $(activatedShortcut).css('background-color', 'rgb(77, 77, 77)');
                $(activatedTab).css('display', 'block');
                $('#mainContent-wrap').css('width', 'calc(100% - 300px - 25px - 5px)');
            }
            //Если была активна
            else{
                $(this).css('background-color', '');
                activatedTab.css('display', 'none');
                $('#mainContent-wrap').css('width', 'calc(100% - 25px - 5px)');
            }
        });
    });
    
    function update(){
        var channelBas_path = $('#chanelBas_path').val();
        var displayDat_path = $('#displayDat_path').val();
        var channels = getChannels(channelBas_path);
        var displays = getDisplays(displayDat_path);
        fillTheSelect($('#channels'), channels);
        fillTheSelect($('#display'), displays);
        var activeDisplay = $('#display option:selected').val();
        var activeChannels = displays[activeDisplay].channels;
        selectChannels(activeChannels);

        var dataArh_path = $('#ddmmArh_path').val();

        preloader.visible(true);
        $(".controlItem").prop("disabled", true);

        updatePlot(plot, activeChannels, dataArh_path, channels)
            .then(() => {
                $(".controlItem").prop("disabled", false);
                preloader.visible(false);
            })
    }
</script>


<?php require 'php/common/foot.php'; ?>

