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
    chart.margin(-5, -25, -25, -15);

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
    plot.legend().title(false);
    plot.crosshair().yStroke(null);
    plot.xMinorGrid(true);
    plot.yMinorGrid(true);
    plot.xAxis().minorLabels().format('{%tickValue}{dateTimeFormat:HH:mm:ss}');
    plot.xAxis().minorLabels().fontColor('black');
    plot.xAxis().labels(false);
    plot.yAxis().labels().fontColor('black');
    plot.yAxis().ticks(false);
    plot.yAxis().stroke('black');
    plot.xAxis().background().enabled(true).stroke('none').fill('none');
    plot.xAxis().background().topStroke('1 black');

    chart.draw();

    preloader = anychart.ui.preloader();
    preloader.render(document.getElementById("chart"));

    $('input[name=techZone]:first').prop('checked', true);
    var techZone = $('input[name=techZone]').val();
    var date = $('[name=date]').val();
    var workDir_path = getWorkDir(techZone, date);

    if(typeof workDir_path == 'string'){
        var channelBas_path =  workDir_path + '/chanel.bas';
        var displayDat_path =  workDir_path + '/display.dat';
        var channels = getChannels(channelBas_path);
        setChannelList(channels);
        var displays = getDisplays(displayDat_path);
        setDisplayList(displays);
        var activeDisplay = $('[name=display] option:selected').val();
        var activeChannels_old = [];
        var activeChannels = displays[activeDisplay].channels;
        selectChannels(activeChannels);
        
        preloader.visible(true);
        $("select").prop("disabled", true);
        var dateArr = date.split('-');
        var dataArh_path = workDir_path + '/' + dateArr[0] + '/' + dateArr[2] + dateArr[1] + '.arh';
        updatePlot(plot, activeChannels_old, activeChannels, dataArh_path, channels)
            .then(() => {
                $("select").prop("disabled", false);
                preloader.visible(false);
            })

        $('[name=techZone]').change(() => {
            $('.errMessage').remove();
            $('input, select').prop('disabled', false);

            var techZone = $('[name=techZone]:checked').val();
            var workDir_path = getWorkDir(techZone, date);

            if(typeof workDir_path == 'string'){
                var channelBas_path =  workDir_path + '/chanel.bas';
                var displayDat_path =  workDir_path + '/display.dat';
                var channels = getChannels(channelBas_path);
                setChannelList(channels);
                var displays = getDisplays(displayDat_path);
                setDisplayList(displays);
                var activeDisplay = $('[name=display] option:selected').val();
                var activeChannels_old = [];
                var activeChannels = displays[activeDisplay].channels;
                selectChannels(activeChannels);
                
                preloader.visible(true);
                $("select").prop("disabled", true);
                var dateArr = date.split('-');
                var dataArh_path = workDir_path + '/' + dateArr[0] + '/' + dateArr[2] + dateArr[1] + '.arh';
                updatePlot(plot, activeChannels_old, activeChannels, dataArh_path, channels)
                    .then(() => {
                        $("select").prop("disabled", false);
                        preloader.visible(false);
                    })
            }
            else{
                var errMsg = workDir_path[1];
                showErrMessage($('#chart'), errMsg);
                $('input, select').prop('disabled', true);
                $('[name=techZone]').prop('disabled', false);
            }
        });

        //Возможность выхода из фокуса поля "date" по Enter-у
        $('form').submit(function(event) {
            event.preventDefault();
            $('[name=date]').blur();
        });

        chart.listen('click', function() {
            $('input:focus').blur();
        });

        $('[name=date]').blur(function(){
            var newDate = $('[name=date]').val();
            
            if (newDate != date) {
                var preDate = date;
                date = newDate;

                var date_num = parseInt(date.replace(/-/g, ''));
                var preDate_num = parseInt(preDate.replace(/-/g, ''));
                var workDir_name = workDir_path.slice(workDir_path.lastIndexOf('/') + 1);
                var workDirDate_num = parseInt(workDir_name.replace(/-/g, ''));

                if ((date_num < workDirDate_num) || (date_num > preDate_num)) {
                    
                    workDir_path = getWorkDir(techZone, date);
                    var channelBas_path =  workDir_path + '/chanel.bas';
                    var displayDat_path =  workDir_path + '/display.dat';
                    var channels = getChannels(channelBas_path);
                    setChannelList(channels);
                    var displays = getDisplays(displayDat_path);
                    setDisplayList(displays);
                }

                activeChannels_old = activeChannels;
                activeChannels = [];

                preloader.visible(true);
                var dateArr = date.split('-');
                var dataArh_path = workDir_path + '/' + dateArr[0] + '/' + dateArr[2] + dateArr[1] + '.arh';
                updatePlot(plot, activeChannels_old, activeChannels, dataArh_path, channels);
                
                activeChannels = activeChannels_old;
                activeChannels_old = [];

                var dateArr = date.split('-');
                var dataArh_path = workDir_path + '/' + dateArr[0] + '/' + dateArr[2] + dateArr[1] + '.arh';
                updatePlot(plot, activeChannels_old, activeChannels, dataArh_path, channels)
                    .then(() => preloader.visible(false));
            }
        });

        $('[name=display]').change(function() {
            $('[name=channels] option:selected').prop('selected', '');
            activeDisplay = $('[name=display] option:selected').val();
            activeChannels_old = activeChannels;
            activeChannels = displays[activeDisplay].channels;
            selectChannels(activeChannels);

            preloader.visible(true);
            updatePlot(plot, activeChannels_old, activeChannels, dataArh_path, channels)
                .then(() => preloader.visible(false));
        });

        $('[name=channels]').change(function() {
            activeChannels_old = activeChannels;
            activeChannels = $('[name=channels]').val();
            
            preloader.visible(true);
            updatePlot(plot, activeChannels_old, activeChannels, dataArh_path, channels)
                .then(() => preloader.visible(false));
        });
    }
    else{
        var errMsg = workDir_path[1];
        showErrMessage($('#chart'), errMsg);
    }

    $('#shortcut-techZones').click(function(){
        if($('#tab-techZones').css('display') == 'none'){
            $('.tab').css('display', 'none');
            $('.shortcut').css('background-color', '');

            $('#shortcut-techZones').css('background-color', 'rgb(77, 77, 77)');
            $('#tab-techZones').css('display', 'block');
            $('#mainContent-wrap').css('width', 'calc(100% - 300px - 25px - 5px)');
        }
        else{
            $('#shortcut-techZones').css('background-color', '');
            $('#tab-techZones').css('display', 'none');
            $('#mainContent-wrap').css('width', 'calc(100% - 25px - 5px)');
        }
    });

    $('#shortcut-channels').click(function(){
        if($('#tab-channels').css('display') == 'none'){
            $('.tab').css('display', 'none');
            $('.shortcut').css('background-color', '');

            $('#shortcut-channels').css('background-color', 'rgb(77, 77, 77)');
            $('#tab-channels').css('display', 'block');
            $('#mainContent-wrap').css('width', 'calc(100% - 300px - 25px - 5px)');
        }
        else{
            $('#shortcut-channels').css('background-color', '');
            $('#tab-channels').css('display', 'none');
            $('#mainContent-wrap').css('width', 'calc(100% - 25px - 5px)');
        }
    });
});