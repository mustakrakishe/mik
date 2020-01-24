function getWorkDir(techZone, date){
    var workDir = false;
    $.ajax({
        url: "../php/chart/getWorkDir.php",
        data: {
            techZone: techZone,
            date: date
        },
        type: "GET",
        dataType: "json",
        async: false
    })
    .done(function (response) {
        workDir = response;
    })
    .fail(function () {
        workDir =
            'Ошибка запроса доступа к рабочей директории.' + '\n' +
            'Запрашиваемая тех. зона: ' + techZone + '\n' +
            'Запрашиваемая дата: ' + date;
    })
    return workDir;
}

function getChannels(path) {
    var channels = false;

    $.ajax({
        url: "../php/chart/getChannels.php",
        data: { path: path },
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

function getDisplays(path) {
    var displays = [];

    $.ajax({
        url: "../php/chart/getDisplays.php",
        data: { path: path },
        type: "GET",
        dataType: "json",
        async: false
    })
    .done(function (response) {
        displays = response;
    })
    .fail(function () {
        console.log("Ошибка запроса информации о дислеях.");
        displays = false;
    })

    return displays;
}

function fillTheSelect(select, objects) {
    if(objects){
        objects.forEach(function (object, objectNum) {
            select.append($('<option value="' + objectNum + '">' + object.name + '</option>'));
        })
    }
    else{
        console.log('Невозможно заполнить список ' + select.attr('name') + '. Данные для заполнения не получены.');
    }
}

function selectChannels(channels) {
    channels.forEach(function (channel, i, channels) {
        $('[name=channels] :nth-child(' + (parseInt(channel) + 1) + ')').prop("selected", "selected");
    })
};

function getChannelData(channels, path) {
    return new Promise(resolve => {
        $.ajax({
            url: "../php/chart/getChannelData.php",
            data: {
                channels: channels,
                path: path
            },
            type: "GET",
            dataType: "json"
        })
        .done(function (response) {
            resolve(response);
        })
        .fail(function (xhr, status, errorThrown) {
            console.log('channels: ' + channels);
            console.log('path: ' + path);
            alert(
                'Ошибка запроса данных каналов.\n' +
                "Error: " + errorThrown + '\n' +
                "Status: " + status + '\n' +
                xhr
            );
        });
    })

    
}

function getChannelsInfo(channels, activeChannels) {
    var channelsInfo = [];
    activeChannels.forEach(function (channelNum) {
        var channelInfo = channels[channelNum];
        channelInfo['id'] = channelNum;
        channelsInfo.push(channels[channelNum]);
    })

    return channelsInfo;
}

function updatePlot(plot, channelList, channelData_path, channels){
    return new Promise(resolve => {
        var channelList_old = [];
        for (var seriesNum = 0; seriesNum < plot.getSeriesCount(); seriesNum++){
            channelList_old.push(plot.getSeriesAt(seriesNum).id());
        }

        channelsToAdd = channelList.filter(function(element){
            return !(channelList_old.includes(element));
        });
        
        channelsToDelete = channelList_old.filter(function(element){
            if(!(channelList.includes(element))){
                return channelList_old.includes(element);
            }
        });

        if(channelsToDelete.length){
            channelsToDelete.forEach(function (channelNum){
                plot.removeSeries(channelNum);
            });
        }
        
        if(channelsToAdd.length){
            var channelsInfo = getChannelsInfo(channels, channelsToAdd);
            getChannelData(channelsToAdd, channelData_path)
            .then(channelData => { 
                addSeries(plot, channelData, channelsInfo);
            })
            .then(() => {
                resolve();
            })
        }
        else{
            resolve();
        }
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
            yAxis.scale(yScale);
            serie.yScale(yScale);

            serie.legendItem().format("{%seriesName}: {%value} " + serieInfo.units);
        })

        resolve();
    })
}

function showErrMessage(container, errText){
    container.append('<div class="errMessage"><p>' + errText + '</p></div>');
}