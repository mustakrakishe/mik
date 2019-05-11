<?php
    function getDbPoints ($date, $timeB, $timeE){
        require 'php/db_connect.php';
        $dbc = db_connect();
        $query = "SELECT `date`, `value` FROM `channel1` WHERE (`date` BETWEEN '$date $timeB' AND '$date $timeE');";
        $result = mysqli_query($dbc, $query);
        $dbPoints = [];
        while($row = mysqli_fetch_array($result)){
            array_push($dbPoints, [$row['date'], $row['value']]);
        }
        mysqli_close($dbc);
        return $dbPoints;
    }

    function getArchPoints ($date, $timeB, $timeE, $channel){
        $archPoints = [];
        $channel--;

        $SIZE_CHANNEL_COUNT = 2;
        $SIZE_DATE = 16;
        $SIZE_CONF = 61;
        $SIZE_VALUE = 4;
        
        $dateArr = (explode('-', $date));
        $fileName = $dateArr[2].$dateArr[1].'.arh';
        $path = "data/$dateArr[0]/";
        $fileFullName = $path.$fileName;

        if (!file_exists($fileFullName)){
            return false;
        }
        else{
            $file_data = fopen($fileFullName, 'rb');
            $channelCount = unpack('S*', fread($file_data, $SIZE_CHANNEL_COUNT), 0)[1];
            $size_serviceData = $SIZE_CHANNEL_COUNT+$SIZE_DATE+$SIZE_CONF*$channelCount;
            $PointTotalCount = (filesize($fileFullName)-$size_serviceData)/($SIZE_VALUE*$channelCount)-1;
            $interval = 86400/$PointTotalCount;
            $shift = $SIZE_VALUE*$channelCount;

            fseek($file_data, $size_serviceData);

            for ($i=0; $i<$PointTotalCount; $i++){
                $time = gmdate('H:i:s',  $interval*$i);
                if ($time < $timeB){
                    continue;
                }
                elseif ($time <= $timeE){
                    fseek($file_data, $size_serviceData + $shift * $i + $SIZE_VALUE * $channel);
                    $value = round(unpack('f*', fread($file_data, $SIZE_VALUE), SEEK_SET)[1], 3);
                    array_push($archPoints, [$time, $value]);
                }
                elseif($time > $timeE){
                    break;
                }
            }
            
        fclose($file_data);
        return $archPoints;
        }
        
    }

    //Сокращает количество точек массива $dbPoints до значения $chartPointsCount
    //Брутся значения по максимальному шагу увеличения
    function getInstantPoints ($dbPoints, $chartPointsCount){
        $dbPointCount = count($dbPoints);

        if ($dbPointCount > $chartPointsCount){
            $chartPointsCount_tmp = $chartPointsCount-1;
            $interval = floor(($dbPointCount-1)/($chartPointsCount_tmp));
            $chartPoints = [];
            for ($i = 0; $i < $chartPointsCount_tmp; $i++){
                array_push($chartPoints, $dbPoints[$i * $interval]);
            }
            array_push($chartPoints, end($dbPoints));
        }
        else{
            $chartPoints = $dbPoints;
        }
        return $chartPoints;
    }
?>