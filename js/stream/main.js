$(document).ready(function (){
    var containerId = 'chart';
    var displayedPointsCount = $('#displayedInterval').val();
    
    anychart.exports.server("http://localhost:2000");
    anychart.format.inputLocale('ru-ru');
    anychart.format.outputLocale('ru-ru');

    //create chart
    var chart = configChart(anychart.stock());
    var plot = chart.plot();
    chart.container(containerId);
    chart.draw();

    var timeOffset = 0 - new Date().getTimezoneOffset() / 60;   //getTimezoneOffset() возвращает UTC - current = -2
    var dataTable = anychart.data.table(0, 0, timeOffset);

    preloader = anychart.ui.preloader();
    preloader.render(document.getElementById(containerId));

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
    var channelsProp = getChannelsProp(channels, activeChannels);
    
    preloader.visible(true);
    $(".controlItem").prop("disabled", true);

    var today = new Date();
    var lastSecond = today.getHours()*3600 + today.getMinutes()*60 + today.getSeconds();
    var firstSecond = 0;//lastSecond - displayedPointsCount + 1;
    if(firstSecond < 0) firstSecond = 0;
    var channelData = [];
    
    parseArhFile(dataArh_path, activeChannels, firstSecond, lastSecond)
        .then(channelData => {
            if (channelData){     //ИСПРАВИТЬ! Если мало точек на момент запуска - вылетает в else
                dataTable.addData(channelData);
                addSeries(plot, dataTable, channelsProp)
                    .then(() => {
                        $(".controlItem").prop("disabled", false);
                        preloader.visible(false);

                        var lastAddedPointTime = channelData[channelData.length - 1][0]; //php возвращает timestamp в "с", а js работает с timestamp в "мс"
                        // var streamTimer = startStream(dataArh_path, activeChannels, dataTable, lastAddedPointTime);
                    })
            }
            else{
                //var lastAddedPointTime = fileLastModDate;
                //ИСПРАВИТЬ! Доработай проверку существования файлов .arh, .dat, .bas
                console.log('Шось не то' + channelData);
                preloader.visible(false);
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
});