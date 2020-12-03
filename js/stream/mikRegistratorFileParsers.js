function getChannels(path) {
    var channels = false;

    $.ajax({
        url: "../php/chart/ajaxHandlers/getChannels.php",
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
        url: "../php/chart/ajaxHandlers/getDisplays.php",
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

function parseArhFile(path, channels, firstSecond, lastSecond) {
    return new Promise(resolve => {
        $.ajax({
            url: "../php/chart/ajaxHandlers/parseArhFile.php",
            data: {
                path: path,
                channels: channels,
                firstSecond: firstSecond,
                lastSecond: lastSecond
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