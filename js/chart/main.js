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

    var date = $('[name=date]').val();
    var channels = getChannels(date);
    setChannelList(channels);
    var displays = getDisplays(date);
    setDisplayList(displays);
    var activeDisplay = $('[name=display] option:selected').val();
    var activeChannels_old = [];
    var activeChannels = displays[activeDisplay].channels;
    selectChannels(activeChannels);
    
    preloader = anychart.ui.preloader();
    preloader.render(document.getElementById("chart"));
    
    preloader.visible(true);
    updatePlot(plot, activeChannels_old, activeChannels, date, channels)
        .then(() => preloader.visible(false));

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
        if (date != newDate) {
            if (date.substring(0, 4) != newDate.substring(0, 4)) {
                channels = getChannels(newDate);
                setChannelList(channels);
                displays = getDisplays(date);
                setDisplayList(displays);
            }
            date = newDate;

            activeChannels_old = activeChannels;
            activeChannels = [];

            preloader.visible(true);
            updatePlot(plot, activeChannels_old, activeChannels, date, channels)
                .then(() => preloader.visible(false));
            
            activeChannels = activeChannels_old;
            activeChannels_old = [];

            preloader.visible(true);
            updatePlot(plot, activeChannels_old, activeChannels, date, channels)
                .then(() => preloader.visible(false));
        }
    });

    $('[name=display]').change(function() {
        $('[name=channels] option:selected').removeAttr('selected');
        activeDisplay = $('[name=display] option:selected').val();
        activeChannels_old = activeChannels;
        activeChannels = displays[activeDisplay].channels;
        selectChannels(activeChannels);

        preloader.visible(true);
        updatePlot(plot, activeChannels_old, activeChannels, date, channels)
            .then(() => preloader.visible(false));
    });

    $('[name=channels]').change(function() {
        activeChannels_old = activeChannels;
        activeChannels = $('[name=channels]').val();
        
        preloader.visible(true);
        updatePlot(plot, activeChannels_old, activeChannels, date, channels)
            .then(() => preloader.visible(false));
    });

    $('#shortcut-channels').click(function(){
        if($('#tab-channels').css('display') == 'none'){
            $('#shortcut-channels').css('background-color', 'rgb(77, 77, 77)');
            $('#tab-channels').css('display', 'block');
            $('#mainContent-wrap').css('width', 'calc(100% - 300px - 25px - 5px)');
            $('.anychart-loader').css('width', 'calc(100% - 300px - 25px - 5px)');
        }
        else{
            $('#shortcut-channels').css('background-color', '');
            $('#tab-channels').css('display', 'none');
            $('#mainContent-wrap').css('width', 'calc(100% - 25px - 5px)');
            $('.anychart-loader').css('width', 'calc(100% - 25px - 5px)');
        }
    });
});