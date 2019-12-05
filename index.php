<?php require 'php/common/head.php'; ?>
<script src='js/libraries/jquery-3.4.1.min.js'></script>

<h1>Главная</h1>

<?php
    //header('location: chart.php');
?>

<script>
    let promise = new Promise(function(resolve, reject) {
        resolve("done!");
    });

    // resolve запустит первую функцию, переданную в .then
    promise.then(result => {alert(result)});

</script>



<?php require 'php/common/foot.php'; ?>