$(document).ready(function () {
    var date = $('[name=date]').val();
    var displays = getDisplays(date);
    //setDisplayList();
});

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
        .fail(function (xhr, status, errorThrown) {
            alert("Ошибка запроса информации о дислеях.");
            console.log("Error: " + errorThrown);
            console.log("Status: " + status);
            console.dir(xhr);
        })

    return displays;
}