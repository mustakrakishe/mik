function startStream(dataArh_path, activeChannels, dataTable, lastAddedPointTime){

    var streamTimer = setInterval(function(){
        getFileLastModDate(dataArh_path)
        .then(fileLastModDate => {
            fileLastModDate *= 1000;

            if(fileLastModDate > lastAddedPointTime){
                var lastAddedPointTime_obj = new Date(lastAddedPointTime);
                var fileLastModDate_obj = new Date(fileLastModDate);
                var firstSecond = lastAddedPointTime_obj.getHours() * 3600 + lastAddedPointTime_obj.getMinutes() * 60 + lastAddedPointTime_obj.getSeconds() + 1;
                var lastSecond = fileLastModDate_obj.getHours() * 3600 + fileLastModDate_obj.getMinutes() * 60 + fileLastModDate_obj.getSeconds();
                
                

                parseArhFile(dataArh_path, activeChannels, firstSecond, lastSecond)
                    .then(newData => {
                        if(newData){
                            console.log('new data: ' + newData);
                            dataTable.addData(newData);
                            dataTable.removeFirst(newData.length);                      //ИСПРАВИТЬ! Если на момент запуска было только 3 точки, то график продолжает отображать 3 точки
                            lastAddedPointTime = newData[newData.length - 1][0];
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

function getFileLastModDate(path){
    return new Promise(resolve => {
        $.ajax({
            url: "../php/chart/getFileLastModDate.php",
            data: {path: path},
            type: "GET",
            dataType: "json"
        })
        .done(function (fileLastModDate) {
            resolve (fileLastModDate);
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
            serie.tooltip().format("{%seriesName}: {%value} " + seriesProp.units);
        })

        resolve();
    })
}

function configChart(chart){
    

    return chart;
}