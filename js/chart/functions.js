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

function setChannelList(channels) {
    if(channels){
        channels.forEach(function (channel, i, channels) {
            $('[name=channels]').append($('<option value="' + i + '">' + channel.name + '</option>'));
        })
    }
    else{
        console.log('Невозможно заполнить список каналов. Массив каналов не получен.');
    }
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

function setDisplayList(displays) {
    displays.forEach(function (display, i, displays) {
        $('[name=display]').append($('<option value="' + i + '">' + display.name + '</option>'));
    })
};

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

async function updatePlot(plot, activeChannels_old, activeChannels, date, channels){
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

        channelName = getChannelNames(channels, channelsToAdd);

        var channelData = await getChannelData(channelsToAdd, date);
        await addSeries(plot, channelData, channelsToAdd, channelName);
    }
}

async function addSeries(chart, data, id, names) {
    var dataTable = anychart.data.table(0, 0, 2);
    dataTable.addData(data);

    id.reduce(function(value_pre, value, channelNum){
        return value_pre
            .then(() => chart.line(dataTable.mapAs({ value: channelNum + 1 })).name(names[channelNum]).id(id[channelNum]));
        
    }, Promise.resolve());
}

function showErrMessage(container, errText){
    container.append('<div class="errMessage"><p>' + errText + '</p></div>');
}