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

    plot.legend().fontColor('Black').position('bottom').itemsLayout("horizontal-expandable").title(false);
    plot.crosshair().yStroke(null);
    plot.xMinorGrid(true);
    plot.yMinorGrid(true);
    plot.xAxis().labels(false).minorLabels().format('{%tickValue}{dateTimeFormat:HH:mm:ss}').fontColor('black');
    plot.yAxis().ticks(false).stroke('black').labels().fontColor('black');
    plot.xAxis().background().enabled(true).stroke('none').fill('none').topStroke('1 black');

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
    // cover only chart container
    preloader.render(document.getElementById("chart"));
    // show preloader
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
            updatePlot(plot, activeChannels_old, activeChannels, date, channels);
            
            activeChannels = activeChannels_old;
            activeChannels_old = [];
            updatePlot(plot, activeChannels_old, activeChannels, date, channels);
        }
    });

    $('[name=display]').change(function() {
        $('[name=channels] option:selected').removeAttr('selected');
        activeDisplay = $('[name=display] option:selected').val();
        activeChannels_old = activeChannels;
        activeChannels = displays[activeDisplay].channels;
        selectChannels(activeChannels);
        updatePlot(plot, activeChannels_old, activeChannels, date, channels);
    });

    $('[name=channels]').change(function() {
        activeChannels_old = activeChannels;
        activeChannels = $('[name=channels]').val();
        updatePlot(plot, activeChannels_old, activeChannels, date, channels);
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

function getChannels(date) {
    var channels = false;

    $.ajax({
        url: "../php/chart/getChannels.php",
        data: { date: date },
        type: "GET",
        dataType: "json",
        async: false
    })
        .done(function (response) {
            channels = response;
        })
        .fail(function () {
            alert("Ошибка запроса информации о каналах.");
        })

    return channels;
}

function setChannelList(channels) {
    channels.forEach(function (channel, i, channels) {
        $('[name=channels]').append($('<option value="' + i + '">' + channel.name + '</option>'));
    })
}

function getDisplays(date) {
    var displays = false;

    $.ajax({
        url: "../php/chart/getDisplays.php",
        data: { date: date },
        type: "GET",
        dataType: "json",
        async: false
    })
        .done(function (response) {
            displays = response;
        })
        .fail(function () {
            alert("Ошибка запроса информации о дислеях.");
        })

    return displays;
}

function setDisplayList(displays) {
    displays.forEach(function (display, i, displays) {
        $('[name=display]').append($('<option value="' + i + '">' + display.name + '</option>'));
    })
};

function selectChannels(channels) {
    channels.forEach(function (channel, i, channels) {
        $('[name=channels] :nth-child(' + (parseInt(channel) + 1) + ')').attr("selected", "selected");
    })
};

function getChannelData(channels, date) {
    $.ajax({
        url: "../php/chart/getChannelData.php",
        data: {
            channels: channels,
            date: date
        },
        type: "GET",
        dataType: "json",
        async: false
    })
    .done(function (response) {
        return response;
    })
    .fail(function (xhr, status, errorThrown) {
        alert(
            'Ошибка запроса данных каналов.\n' +
            "Error: " + errorThrown + '\n' +
            "Status: " + status + '\n' +
            xhr
        );
    });
}

function getChannelNames(channels, activeChannels) {
    var channelNames = [];
    activeChannels.forEach(function (channel) {
        channelNames.push(channels[channel].name);
    })

    return channelNames;
}

function updatePlot(plot, activeChannels_old, activeChannels, date, channels){
    
    return new Promise(function(resolve, reject) {
        channelsToAdd = activeChannels.filter(function(element){
            return !(activeChannels_old.includes(element));
        });
        
        channelsToDelete = activeChannels_old.filter(function(element){
            if(!(activeChannels.includes(element))){
                return activeChannels_old.includes(element);
            }
        });
        

        if(channelsToDelete.length){
            
            channelsToDelete.forEach(function (channelNum){
                plot.removeSeries(channelNum);
            });
        }
        
        if(channelsToAdd.length){

            var channelName = getChannelNames(channels, channelsToAdd);
            var channelData = getChannelData(channelsToAdd, date);
            addSeries(plot, channelData, channelsToAdd, channelName);
        };

        resolve();
    })
    
    function addSeries(chart, data, id, names) {
        var dataTable = anychart.data.table(0, 0, 2);
        dataTable.addData(data);
    
        id.forEach(function(value, channelNum){
            chart.line(dataTable.mapAs({ value: channelNum + 1 })).name(names[channelNum]).id(id[channelNum]);
        });
    }
}