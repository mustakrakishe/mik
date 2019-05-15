$(document).ready(function () {
    var date = $('[name=date]').val();
    getDisplays(date);
});

function getDisplays(date) {
    $.ajax({
        url: "../php/chart/getDisplays.php",
        data: { date: date },
        type: "GET",
        dataType: "json",
    })
        .done(function (displays) {
            alert(displays[0].name);
        })
        .fail(function (xhr, status, errorThrown) {
            alert("Ошибка запроса информации о дислеях.");
            console.log("Error: " + errorThrown);
            console.log("Status: " + status);
            console.dir(xhr);
        })
}