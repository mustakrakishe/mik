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

function getChannelNames(channels, activeChannels) {
    var channelNames = [];
    activeChannels.forEach(function (channel) {
        channelNames.push(channels[channel].name);
    })

    return channelNames;
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
            channelName = getChannelNames(channels, channelsToAdd);
            getChannelData(channelsToAdd, channelData_path)
            .then(channelData => {
                addSeries(plot, channelData, channelsToAdd, channelName);            
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

function addSeries(chart, data, id, names) {
    return new Promise(resolve => {
        //var colorMap = ['#008000', '#CC6600', '#FF0000', '#008080', '#FF00FF', '#333333‬', '#8000FF', '#663300']

        var dataTable = anychart.data.table(0, 0, 2);
        dataTable.addData(data);

        id.reduce(function(value_pre, value, channelNum){
            return value_pre
                .then(() => chart.line(dataTable.mapAs({ value: channelNum + 1 })).name(names[channelNum]).id(id[channelNum]));
            
        }, Promise.resolve())
        .then(() => resolve());
            
    })
}

function showErrMessage(container, errText){
    container.append('<div class="errMessage"><p>' + errText + '</p></div>');
}