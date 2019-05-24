$(document).ready(function () {
    var date = $('[name=date]').val();
    var channels = getChannels(date);
    setChannelList(channels);
    var displays = getDisplays(date);
    setDisplayList(displays);
    var activeDisplay = $('[name=display] option:selected').val();
    //Преобразовать объект activeDisplay в массив
    var activeChannels = Array.from(displays[activeDisplay].channels);
    selectChannels(activeChannels);
    var channelData = getChannelData(activeChannels, date);
    //buildGraph(channelData);
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
        .fail(function () {
            alert("Ошибка запроса данных каналов.");
        })

    return channelData;
}

function buildGraph(graphData){
    var chart = anychart.line();
        chart.defaultSeriesType("line");

        graphData.forEach(function (channelPoints, i, graphData){
            var data = anychart.data.set(channelPoints);
            var series = data.mapAs({x: 0, value: 1});
            chart.addSeries(series);
        });
        
        var xAxis = chart.xAxis();
        xAxis.title("Абсолютное значение параметра");
        var yAxis = chart.yAxis();
        yAxis.title("Время");
        
        chart.container("container");
        chart.draw();
}