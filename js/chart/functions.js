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
    console.log('getChannelData() starts...');

    return new Promise(resolve => {
        $.ajax({
            url: "../php/chart/getChannelData.php",
            data: {
                channels: channels,
                date: date
            },
            type: "GET",
            dataType: "json"
        })
        .done(function (response) {
            console.log('getChannelData() is done!');
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
        addSeries(plot, channelData, channelsToAdd, channelName);
    }
}

function addSeries(chart, data, id, names) {
    console.log('addSeries() starts...');
    var dataTable = anychart.data.table(0, 0, 2);
    dataTable.addData(data);

    id.forEach(function(value, channelNum){
        chart.line(dataTable.mapAs({ value: channelNum + 1 })).name(names[channelNum]).id(id[channelNum]);
    })
    console.log('addSeries() is done!');
}