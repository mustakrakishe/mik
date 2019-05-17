<?php
    function getChannels($path){
        $channels = [];

        class channel{
            public $name = '';
            public $units = '';

            function __construct($name, $units){
                $this->name = $name;
                $this->units = $units;
            }
        }
        $handle = fopen($path, 'r');

        while(!feof($handle)){
            $string = iconv("windows-1251","utf-8", fgets($handle));
            /*list(
                $name,
                ,
                $units,
                $scaleL,
                $scaleH,
                $techL,
                $techH,
                $crushL,
                $crushH,
                $arch
            ) = preg_split('@[\\\/(,);\[|#$:~]|(]R>0<)@', $string);*/
            
            if((int)substr($string, strpos($string, '<') + 1, 1)){
                list($name, , $units, , $arch) = preg_split('@[\\\/(<#]@', $string);
                array_push($channels, new channel($name, $units));
            }
        }

        fclose($handle);
        return $channels;
    }

    function getDisplays($path){
        $displays = [];

        class display{
            public $num = null;
            public $name = '';
            public $channels = [];

            function __construct($num, $name, $channels){
                $this->num = $num;
                $this->name = $name;
                $this->channels = $channels;
            }
        }

        $handle = fopen($path, 'r');

        fgets($handle);
        while(!feof($handle)){
            $num = (int)substr(fgets($handle), 1);

            if($num > 1279 && $num < 1536){
                fgets($handle);
                $channels = explode(';', substr(fgets($handle), 2, -3));
                $name = substr(iconv("windows-1251","utf-8", fgets($handle)), 2, -2);

                array_push($displays, new display($num, $name, $channels));
                
                fgets($handle);
            }
            else{
                for($i = 1; $i < 5; $i++){
                    fgets($handle);
                }
            }
        }

        fclose($handle);

        return $displays;
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
?>