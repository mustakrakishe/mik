$(document).ready(function () {
    var date = $('[name=date]').val();
    var channels = getChannels(date);
    setChannelList(channels);
    var displays = getDisplays(date);
    setDisplayList(displays);
    var activeDisplay = $('[name=display] option:selected').val();
    var activeChannels = displays[activeDisplay].channels;
    selectChannels(activeChannels);
    var channelNames = getChannelNames(channels, activeChannels);
    var channelData = getChannelData(activeChannels, date);
    buildGraph(channelData, channelNames);

    $('form').submit(function (event) {
        event.preventDefault();
        var newDate = $('[name=date]').val();
        if (date != newDate) {
            if (date.substring(0, 4) != newDate.substring(0, 4)) {
                channels = getChannels(newDate);
                setChannelList(channels);
                displays = getDisplays(date);
                setDisplayList(displays);
            }
            channelData = getChannelData(activeChannels, newDate);
            channelNames = getChannelNames(channels, activeChannels);
            buildGraph(channelData, channelNames);
            date = newDate;
        }
    });

    $('[name=display]').change(function (event) {
        $('[name=channels] option:selected').removeAttr('selected');
        activeDisplay = $('[name=display] option:selected').val();
        activeChannels = displays[activeDisplay].channels;
        selectChannels(activeChannels);
        channelData = getChannelData(activeChannels, date);
        channelNames = getChannelNames(channels, activeChannels);
        buildGraph(channelData, channelNames);
    });

    $('[name=channels]').change(function (event) {
        activeChannels = $('[name=channels]').val();
        channelData = getChannelData(activeChannels, date);
        channelNames = getChannelNames(channels, activeChannels);
        buildGraph(channelData, channelNames);
    });

    $('#shortcut-channels').click(function(){
        if($('#tab-channels').css('display') == 'none'){
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
}

function selectChannels(channels) {
    channels.forEach(function (channel, i, channels) {
        $('[name=channels] :nth-child(' + (parseInt(channel) + 1) + ')').attr("selected", "selected");
    })
}

function getChannelData(channels, date) {
    var channelData = false;

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
            channelData = response;
        })
        .fail(function (xhr, status, errorThrown) {
            alert(
                'Ошибка запроса данных каналов.\n' +
                "Error: " + errorThrown + '\n' +
                "Status: " + status + '\n' +
                xhr
            )
        })

    return channelData;
}

function getChannelNames(channels, activeChannels) {
    var channelNames = [];
    activeChannels.forEach(function (channel) {
        channelNames.push(channels[channel].name);
    })

    return channelNames;
}

function buildGraph(graphData, channelNames) {
    $('#chart').empty();
    var chart = anychart.stock();
    chart.container('chart');
    chart.scroller(false);

    var plot = chart.plot();
    plot.legend().position('bottom');
    plot.legend().itemsLayout("horizontal-expandable");
    plot.legend().title(false);
    chart.crosshair().xLabel(false);
    chart.crosshair().yLabel(false);
    plot.crosshair().yStroke(null);
    plot.xMinorGrid(true);
    plot.yMinorGrid(true);
    chart.margin(-5, -25, -25, -25);

    plot.removeAllSeries();

    var dataTable = anychart.data.table(0, 0, 2);
    dataTable.addData(graphData);

    var channelCount = 8;
    for (var channelNum = 0; channelNum < channelCount; channelNum++) {
        plot.line(dataTable.mapAs({ value: channelNum + 1 })).name(channelNames[channelNum]);
    }


    var interactivity = chart.interactivity();
    interactivity.zoomOnMouseWheel(true);

    chart.plot(0).xAxis().minorLabels().format('{%tickValue}{dateTimeFormat:HH:mm:ss}');
    chart.plot(0).xAxis().labels(false);
    
    chart.xScale().ticks();;
    chart.draw();
}