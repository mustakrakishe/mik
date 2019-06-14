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
                $channels = array_filter(explode(';', substr(fgets($handle), 2, -3)), function($num){
                    return $num>=0;
                });
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

    function getChannelData($path, $channels){
        $channelData = [];

        $SIZE_CHANNEL_COUNT = 2;
        $SIZE_DATE = 16;
        $SIZE_CONF = 61;
        $SIZE_VALUE = 4;
        
        $date = str_split(basename($path, ".arh"), 2);
        filemtime($path);
        $fileHandler = fopen($path, 'rb');
        $file_channelCount = unpack('S*', fread($fileHandler, $SIZE_CHANNEL_COUNT), 0)[1];
        $size_serviceData = $SIZE_CHANNEL_COUNT+$SIZE_DATE+$SIZE_CONF*$file_channelCount;
        $channelPointsCount = (filesize($path)-$size_serviceData)/($SIZE_VALUE*$file_channelCount)-1;
        $interval = 86400/$channelPointsCount;
        $shift = $SIZE_VALUE*$file_channelCount;

        fseek($fileHandler, $size_serviceData);
        foreach($channels as $channel){
            $channelPoints = [];
            for ($i=0; $i<$channelPointsCount; $i++){
                $timestamp = (strtotime(date('Y-m-d', filemtime($path)-1).' 00:00:00') + $interval*$i)*1000;
                
                //$time = gmdate('Y-m-d H:i:s',  $interval*$i);
                fseek($fileHandler, $size_serviceData + $shift * $i + $SIZE_VALUE * $channel);
                $value = round(unpack('f*', fread($fileHandler, $SIZE_VALUE), SEEK_SET)[1], 3);
                array_push($channelPoints, [$timestamp, $value]);
            }
            array_push($channelData, $channelPoints);
        }
            
        fclose($fileHandler);
        return $channelData;
    }

    function getChannelData1($path, $channels){
        $channelData = [];

        $SIZE_CHANNEL_COUNT = 2;
        $SIZE_DATE = 16;
        $SIZE_CONF = 61;
        $SIZE_VALUE = 4;
        
        $date = str_split(basename($path, ".arh"), 2);
        filemtime($path);
        $fileHandler = fopen($path, 'rb');
        $file_channelCount = unpack('S*', fread($fileHandler, $SIZE_CHANNEL_COUNT), 0)[1];
        $size_serviceData = $SIZE_CHANNEL_COUNT+$SIZE_DATE+$SIZE_CONF*$file_channelCount;
        $channelPointsCount = (filesize($path)-$size_serviceData)/($SIZE_VALUE*$file_channelCount)-1;
        $interval = 86400/$channelPointsCount;
        $shift = $SIZE_VALUE*$file_channelCount;

        $timeL = strtotime(date('Y-m-d', filemtime($path)-1).' 00:00:00');
        $channelData[0] = range($timeL, ($timeL + $interval * $channelPointsCount), $interval);

        fseek($fileHandler, $size_serviceData);
        foreach($channels as $channel){
            $channelPoints = [];
            for ($i=0; $i<$channelPointsCount; $i++){
                fseek($fileHandler, $size_serviceData + $shift * $i + $SIZE_VALUE * $channel);
                $value = round(unpack('f*', fread($fileHandler, $SIZE_VALUE), SEEK_SET)[1], 3);
                array_push($channelPoints, $value);
            }
            array_push($channelData, $channelPoints);
        }
            
        fclose($fileHandler);
        return $channelData;
    }
?>