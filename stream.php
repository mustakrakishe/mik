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
    <p>ddmm.arh</p><input type="text" name="ddmmArh_path" id="ddmmArh_path" value="C:\Program Files (x86)\Microl\Mик-Регистратор\<?php echo date('dm') ?>.arh">
    <p>display.dat</p><input type="text" name="displayDat_path" id="displayDat_path" value="C:\Program Files (x86)\Microl\Mик-Регистратор\display.dat">
    <p>chanel.bas</p><input type="text" name="chanelBas_path" id="chanelBas_path" value="C:\Program Files (x86)\Microl\Mик-Регистратор\chanel.bas">
    <p>Отображаемый интервал</p><input type="text" name="displayedInterval" id="displayedInterval" value="15">
    <input type="button" id='updateBtn' value="Обновить">
</div>

<div id="side-bar">
    <div id='shortcut-channels' class='shortcut'><p>Каналы</p></div>
    <div id='shortcut-paths' class='shortcut'><p>Пути</p></div>
</div>


<script>
    $(document).ready(function (){
        var containerId = 'chart';
        var displayedInterval = $('#displayedInterval').val();
        var plot = initializeChart(containerId, displayedInterval).plot();

        var channelBas_path = $('#chanelBas_path').val();
        var displayDat_path = $('#displayDat_path').val();
        var dataArh_path = $('#ddmmArh_path').val();

        var channels = getChannels(channelBas_path);
        var displays = getDisplays(displayDat_path);
        fillTheSelect($('#channels'), channels);
        fillTheSelect($('#display'), displays);

        var activeDisplay = $('#display option:selected').val();
        var activeChannels = displays[activeDisplay].channels;
        selectChannels(activeChannels);
        var channelsInfo = getChannelsInfo(channels, activeChannels);
        
        preloader.visible(true);
        $(".controlItem").prop("disabled", true);

        var today = new Date();
        var lastSecond = today.getHours()*3600 + today.getMinutes()*60 + today.getSeconds();
        var firstSecond = lastSecond - displayedInterval + 1;
        
        parseArhFile(dataArh_path, activeChannels, firstSecond, lastSecond)
        .then(channelData => {
            if (channelData){
                addSeries(plot, channelData, channelsInfo)
                .then(dataTable => {
                    $(".controlItem").prop("disabled", false);
                    preloader.visible(false);

                    var lastAddedPointTime = channelData[channelData.length - 1][0]; //php возвращает timestamp в "с", а js работает с timestamp в "мс"
                ///
                setInterval(function(){
                    getFileLastModDate(dataArh_path)
                    .then(fileLastModDate => {
                        fileLastModDate = fileLastModDate*1000;

                        if(fileLastModDate > lastAddedPointTime){
                            var lastAddedPointTime_obj = new Date(lastAddedPointTime);
                            var fileLastModDate_obj = new Date(fileLastModDate);
                            var fistSecond = lastAddedPointTime_obj.getHours() * 3600 + lastAddedPointTime_obj.getMinutes() * 60 + lastAddedPointTime_obj.getSeconds() + 1;
                            var lastSecond = fileLastModDate_obj.getHours() * 3600 + fileLastModDate_obj.getMinutes() * 60 + fileLastModDate_obj.getSeconds();
                            
                            parseArhFile(dataArh_path, activeChannels, fistSecond, lastSecond)
                            .then(newData => {
                                if(newData){
                                    console.log('new data: ' + newData);
                                    dataTable.addData(newData);
                                    dataTable.removeFirst(newData.length);
                                }
                                
                                /*newData.forEach(momentData =>{
                                    dataTable.addData([momentData]);
                                    dataTable.removeFirst(1);

                                })*/

                                lastAddedPointTime = newData ? newData[newData.length - 1][0] : lastAddedPointTime;
                            })
                        }
                    })
            

            }, 500);
                ///
                })
            }
            else{
                //var lastAddedPointTime = fileLastModDate;
                //Доработай проверку существования файлов .arh, .dat, .bas
            }
             
            
            
            
        });
        
        
        

        /*Отображение боковой панели*/
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

        $('#updateBtn').click(() => {
            updateStartData(plot);
        })
    });

    function initializeChart(containerId, displayedInterval){
        anychart.exports.server("http://localhost:2000");
        anychart.format.inputLocale('ru-ru');
        anychart.format.outputLocale('ru-ru');

        //create chart
        var chart = anychart.stock();
        var plot = chart.plot();

        chart.container(containerId);
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
        chart.xScale().ticksCount(displayedInterval);
        
        plot.yMinorGrid(true);
        plot.yAxis().labels().fontColor('black');
        plot.yAxis().ticks(false);
        plot.yAxis().stroke('black');
        plot.yAxis().labels(false);

        chart.draw();

        preloader = anychart.ui.preloader();
        preloader.render(document.getElementById(containerId));

        return chart;
    }

    function getFileLastModDate(path){
        return new Promise(resolve => {
            $.ajax({
                url: "../php/chart/getFileLastModDate.php",
                data: {
                    path: path
                },
                type: "GET",
                dataType: "json"
            })
            .done(function (fileLastModDate) {
                resolve (fileLastModDate);
            })
            .fail(function (xhr, status, errorThrown) {
                alert(
                    'Ошибка запроса даты последней модификации файла ' + path + '.\n'
                    + "Error: " + errorThrown + '\n'
                    + "Status: " + status + '\n'
                    + xhr
                );
            });
        })
    }

    function addSeries(chart, seriesData, seriesInfo) {
    return new Promise(resolve => {
        var dataTable = anychart.data.table(0, 0, 2);
        dataTable.addData(seriesData);

        seriesInfo.forEach(function(serieInfo, serieNum){
            var mapping = dataTable.mapAs({ value: serieNum + 1 });
            var serie = chart.line(mapping);
            serie.name(serieInfo.name).id(serieInfo.id);

            var yScale = anychart.scales.linear();
            yScale.minimum(serieInfo.scaleL);
            yScale.maximum(serieInfo.scaleH);

            var yAxis = chart.yAxis(serieNum);
            yAxis.scale(yScale).labels().format(function(){
                return (this.value/serieInfo.scaleH*100).toFixed() + '%';
            });
            serie.yScale(yScale);
            
            chart.yAxis().labels(true);
            serie.yScale().ticks().count(6);

            serie.legendItem().format("{%seriesName}: {%value} " + serieInfo.units);
            serie.tooltip().format("{%seriesName}: {%value} " + serieInfo.units);
        })

        resolve(dataTable);
    })
}
</script>


<?php require 'php/common/foot.php'; ?>

