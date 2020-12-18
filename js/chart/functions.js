function configChart(chart){
    chart.scroller(false);
    chart.interactivity().zoomOnMouseWheel(true);
    chart.crosshair().xLabel(false);
    chart.crosshair().yLabel(false);
    chart.margin(-5, -25, -35, -15);

    chart.contextMenu().itemsFormatter(function(items){
        delete items["save-data-as"];
        delete items["share-with"];
        delete items["exporting-separator"];
        delete items["full-screen-enter"];
        delete items["full-screen-separator"];
        delete items["about"];
        return items;
    });

    var plot = chart.plot();

    plot.legend().fontColor('Black');
    plot.legend().position('bottom');
    plot.legend().itemsLayout("horizontal-expandable");
    plot.legend().padding().top(35);
    plot.legend().title(false);
    plot.crosshair().yStroke(null);

    plot.xMinorGrid(true);
    plot.xAxis().minorLabels().format('{%tickValue}{dateTimeFormat:HH:mm:ss}').fontColor('black');
    plot.xAxis().labels(false);
    plot.xAxis().background().enabled(true).stroke('none').fill('none');
    plot.xAxis().background().topStroke('1 black');
    chart.xScale().ticksCount(60);
    chart.xScale('scatter');

    
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    var tomorrow = today;
    tomorrow.setDate(today.getDate() + 1);
    chart.selectRange('2004-01-02 08:56:00','2004-01-15 08:58:00');
    
    plot.yMinorGrid(true);
    plot.yAxis().labels().fontColor('black');
    plot.yAxis().ticks(false);
    plot.yAxis().stroke('black');
    plot.yAxis().labels(false);

    return chart;
}

function getFileLastModDate(path){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "../php/chart/ajaxHandlers/getFileLastModDate.php",
            data: {path: path},
            type: "GET",
            dataType: "json"
        })
        .done(function (response) {
            if(response.status){
                resolve(response.data);
            }
            else{
                reject();
            }
        })
        .fail(function (xhr, status, errorThrown) {
            alert(
                'Ошибка запроса даты последней модификации файла ' + path + '.\n'
                + "Error: " + errorThrown + '\n'
                + "Status: " + status + '\n'
                + xhr
            );
        });
    })
}

function addSeries(chart, dataTable, seriesProp) {
    return new Promise(resolve => {
        seriesProp.forEach(function(seriesProp, serieNum){
            var mapping = dataTable.mapAs({ value: serieNum + 1 });
            var serie = chart.line(mapping);
            serie.name(seriesProp.name).id(seriesProp.id);

            var yScale = anychart.scales.linear();
            yScale.minimum(seriesProp.scaleL);
            yScale.maximum(seriesProp.scaleH);

            var yAxis = chart.yAxis(serieNum);
            yAxis.scale(yScale).labels().format(function(){
                return (this.value / seriesProp.scaleH * 100).toFixed() + '%';
            });
            serie.yScale(yScale);
            
            chart.yAxis().labels(true);
            serie.yScale().ticks().count(6);

            serie.legendItem().format("{%seriesName}: {%value} " + seriesProp.units);
            serie.tooltip().format(function(){
                var value = this.value;
                var data = 'н/д';
                
                if(value !== null){
                    data = value + ' ' + seriesProp.units;
                }

                return this.seriesName + ': ' + data;
            });

        })

        resolve();
    })
}

function startStream(dataArh_path, activeChannels, dataTable, lastAddedPointSecond){

    var streamTimer = setInterval(function(){
        getFileLastModDate(dataArh_path)
            .then(fileLastModTimestamp => {
                fileLastModTimestamp *= 1000;    //php возвращает время в с, а для js надо в мс

                var fileLastModDate = new Date(fileLastModTimestamp);
                fileLastModSecond = fileLastModDate.getHours() * 3600 + fileLastModDate.getMinutes() * 60 + fileLastModDate.getSeconds();

                if(fileLastModSecond > lastAddedPointSecond){
                    parseArhFile(dataArh_path, activeChannels, lastAddedPointSecond, fileLastModSecond)
                        .then(newData => {
                            if(newData){
                                console.log('new data: ' + newData);
                                dataTable.addData(newData);
                                dataTable.removeFirst(1);                      //ИСПРАВИТЬ! Если на момент запуска было только 3 точки, то график продолжает отображать 3 точки
                                lastAddedPointSecond = fileLastModSecond;
                            };
                        });
                };
            });

        $('#streamButton').on('click', function(){
            $(this).unbind('click').text('Начать стрим');
            clearInterval(streamTimer);
            
            $(this).on('click', function(){
                $(this).unbind('click').text('Остановить стрим');
                startStream(dataArh_path, activeChannels, dataTable, lastAddedPointTime);
            });
        });
    }, 500);
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

function getChannelsProp(channels, requiredChannelNumsArr) {
    var channelsProp = [];
    requiredChannelNumsArr.forEach(function (channelNum) {
        var channelProp = channels[channelNum];
        channelProp['id'] = channelNum;
        channelsProp.push(channels[channelNum]);
    })

    return channelsProp;
}