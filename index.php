<?php require 'php/common/head.php'; ?>

<h1>Главная</h1>

<script>
    var a = ['1', '2', '3'];
    a.forEach(function(elem, i, tempA){
        tempA[i] = parseInt(tempA[i]);
    })
    alert(typeof(a[0]));
</script>

<?php require 'php/common/foot.php'; ?>