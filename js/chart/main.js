$(document).ready(function () {

    anychart.exports.server("http://localhost:2000");
    anychart.format.inputLocale('ru-ru');
    anychart.format.outputLocale('ru-ru');

    //create chart
    var chart = anychart.stock();
    var plot = chart.plot();

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

    var techZone = $('#techZone option:selected').val();
    var date = $('#date').val();
    var workDir_path = getWorkDir(techZone, date);

    if(typeof workDir_path == 'string'){
        var channelBas_path =  workDir_path + '/chanel.bas';
        var displayDat_path =  workDir_path + '/display.dat';
        var channels = getChannels(channelBas_path);
        var displays = getDisplays(displayDat_path);
        fillTheSelect($('#channels'), channels);
        fillTheSelect($('#display'), displays);
        var activeDisplay = $('#display option:selected').val();
        var activeChannels = displays[activeDisplay].channels;
        selectChannels(activeChannels);
        
        preloader.visible(true);
        $(".controlItem").prop("disabled", true);
        var dateArr = date.split('-');
        var dataArh_path = workDir_path + '/' + dateArr[0] + '/' + dateArr[2] + dateArr[1] + '.arh';
        anychart.exports.filename(techZone + ' ' + $('#display option:selected').text() + ' ' + date);
        updateDayPlot(plot, activeChannels, dataArh_path, channels)
            .then(() => {
                $(".controlItem").prop("disabled", false);
                preloader.visible(false);
            })

        $('#techZone').change(() => {
            $('#display, #channels').children().remove();
            $('.controlItem').prop('disabled', false);
            plot.removeAllSeries();
            $('.errMessage').remove();

            techZone = $('#techZone option:selected').val();
            
            workDir_path = getWorkDir(techZone, date);

            if(typeof workDir_path == 'string'){
                channelBas_path =  workDir_path + '/chanel.bas';
                displayDat_path =  workDir_path + '/display.dat';
                channels = getChannels(channelBas_path);
                displays = getDisplays(displayDat_path);
                fillTheSelect($('#channels'), channels);
                fillTheSelect($('#display'), displays);
                activeDisplay = $('#display option:selected').val();
                activeChannels = displays[activeDisplay].channels;
                selectChannels(activeChannels);
                
                preloader.visible(true);
                $(".controlItem").prop("disabled", true);
                var dateArr = date.split('-');
                var dataArh_path = workDir_path + '/' + dateArr[0] + '/' + dateArr[2] + dateArr[1] + '.arh';
                anychart.exports.filename(techZone + ' ' + $('#display option:selected').text() + ' ' + date);
                updatePlot(plot, activeChannels, dataArh_path, channels)
                    .then(() => {
                        $(".controlItem").prop("disabled", false);
                        preloader.visible(false);
                    })
            }
            else{
                var errMsg = workDir_path[1];
                showErrMessage($('#chart'), errMsg);
                $('.controlItem').prop('disabled', true);
                $('#techZone').prop('disabled', false);
                $('#display, #channels').append('<option>-- Нет данных --</option>');
            }
        });

        //Возможность выхода из фокуса поля "date" по Enter-у
        $('form').submit(function(event) {
            event.preventDefault();
            $('#date').blur();
        });

        chart.listen('click', function() {
            $('input:focus').blur();
        });

        $('#date').blur(function(){
            var newDate = $('#date').val();
            
            if(newDate != date) {
                var preWorkDir_path = workDir_path;
                date = newDate;
                workDir_path = getWorkDir(techZone, date);

                if (workDir_path != preWorkDir_path) {
                    channelBas_path =  workDir_path + '/chanel.bas';
                    displayDat_path =  workDir_path + '/display.dat';
                    channels = getChannels(channelBas_path);
                    displays = getDisplays(displayDat_path);
                    fillTheSelect($('#channels'), channels);
                    fillTheSelect($('#display'), displays);
                    activeDisplay = $('#display option:selected').val();
                    activeChannels = displays[activeDisplay].channels;
                    selectChannels(activeChannels);
                }

                plot.removeAllSeries();
                preloader.visible(true);
                $(".controlItem").prop("disabled", true);
                var dateArr = date.split('-');
                var dataArh_path = workDir_path + '/' + dateArr[0] + '/' + dateArr[2] + dateArr[1] + '.arh';
                anychart.exports.filename(techZone + ' ' + $('#display option:selected').text() + ' ' + date);
                updatePlot(plot, activeChannels, dataArh_path, channels)
                    .then(() => {
                        $(".controlItem").prop("disabled", false);
                        preloader.visible(false);
                    })
            }
        });

        $('#display').change(function() {
            $('#channels option:selected').prop('selected', '');
            activeDisplay = $('#display option:selected').val();
            activeChannels = displays[activeDisplay].channels;
            selectChannels(activeChannels);

            preloader.visible(true);
            $(".controlItem").prop("disabled", true);
            var dateArr = date.split('-');
            var dataArh_path = workDir_path + '/' + dateArr[0] + '/' + dateArr[2] + dateArr[1] + '.arh';
            anychart.exports.filename(techZone + ' ' + $('#display option:selected').text() + ' ' + date);
            updatePlot(plot, activeChannels, dataArh_path, channels)
                .then(() => {
                    $(".controlItem").prop("disabled", false);
                    preloader.visible(false);
                })
        });

        $('#channels').change(function() {
            activeChannels = $('#channels').val();
            
            preloader.visible(true);
            $(".controlItem").prop("disabled", true);
            var dateArr = date.split('-');
            var dataArh_path = workDir_path + '/' + dateArr[0] + '/' + dateArr[2] + dateArr[1] + '.arh';
            anychart.exports.filename(techZone + ' ' + $('#display option:selected').text() + ' ' + date);
            updatePlot(plot, activeChannels, dataArh_path, channels)
                .then(() => {
                    $(".controlItem").prop("disabled", false);
                    preloader.visible(false);
                })
        });
    }
    else{
        var errMsg = workDir_path[1];
        showErrMessage($('#chart'), errMsg);
    }

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