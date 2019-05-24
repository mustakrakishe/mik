<?php require 'php/common/head.php'; ?>

<h1>Главная</h1>


<script src="js/anychart/anychart-base.min.js"></script>
<script src="js/anychart/anychart-ui.min.js"></script>
<script src="js/anychart/anychart-exports.min.js"></script>

<script>
    var arr = Array.from({
        0: {0: "01.01.2019", 1: 11111.01},
        1: {0: "02.01.2019", 1: 22222.02},
        2: {0: "03.01.2019", 1: 33333.03}
    });

    alert (typeof(arr));
</script>

<?php require 'php/common/foot.php'; ?>