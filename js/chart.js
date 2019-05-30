$(document).ready(function () {
    var date = $('[name=date]').val();
    var channels = getChannels(date);
    setChannelList(channels);
    var displays = getDisplays(date);
    setDisplayList(displays);
    var activeDisplay = $('[name=display] option:selected').val();
    var activeChannels = displays[activeDisplay].channels;
    selectChannels(activeChannels);
    var channelData = getChannelData(activeChannels, date);
    alert (channelData[0][0]);
    buildGraph(channelData);
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
        var num = parseInt(channel) + 1;
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

function buildGraph(graphData) {
    var chart = anychart.stock();

    var arr = [[
        ["2015-12-25", 512.53],
        ["2015-12-26", 511.83],
        ["2015-12-27", 511.22],
        ["2015-12-28", 510.35],
        ["2015-12-29", 510.53],
        ["2015-12-30", 511.43],
        ["2015-12-31", 511.50],
        ["2016-01-01", 511.32],
        ["2016-01-02", 511.70],
    ],[
        ["2015-12-25", 514.88],
        ["2015-12-26", 514.98],
        ["2015-12-27", 515.30],
        ["2015-12-28", 515.72],
        ["2015-12-29", 515.86],
        ["2015-12-30", 515.98],
        ["2015-12-31", 515.33],
        ["2016-01-01", 514.29],
        ["2016-01-02", 514.87]
    ]];
    
    var chart = anychart.stock();
    chart.title('AnyStock Basic Sample');
    chart.container('chart');
    chart.draw();

    var firstPlot = chart.plot(0);
    
    graphData.forEach(function(channel, channelNum, channels){
        var dataTable = anychart.data.table(0);
        dataTable.addData(channels[channelNum]);
        firstPlot.line(dataTable.mapAs({value: 1}));
    })
}