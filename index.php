<?php require 'head.php'; ?>

<h1>Добро пожаловать!</h1>
<p>Вы на главной странице web-приложения контроля технологических параметров производства КЗТВ.</p>
<p>Доступные функции приложения предоставляются на одноимённых страницах, на которые Вы можете перейти по соответствующим ссылкам вверху настоящей страницы.</p>

<?php
    if (isset($_POST['date'])){
        $date = $_POST['date'];
    }
    else{
        $date = '2018-05-11';//date('Y-m-d', mktime());
    }

    if (isset($_POST['timeB'])){
        $timeB = $_POST['timeB'];
    }
    else{
        $timeB = '00:00';
    }

    if (isset($_POST['timeE'])){
        $timeE = $_POST['timeE'];
    }
    else{
        $timeE = '00:10';
    }
?>

<form action="" method="post">
    <input type="date" name="date" value="<?php echo $date; ?>">
    <input type="time" name="timeB" value="<?php echo $timeB; ?>">
    <input type="time" name="timeE" value="<?php echo $timeE; ?>">
    <input type="submit" name="build_btn" hidden="hidden">
</form>

<?php
    require 'functions.php';
    $archPoints = getArchPoints($date, $timeB, $timeE);
    $archInstantPoints = getInstantPoints($archPoints, 800);
    foreach ($archInstantPoints as $point){
        echo 'Время: ', $point[0], '; Значение: ', $point[1], '.<br>';
    }
?>

<?php require 'foot.php'; ?>