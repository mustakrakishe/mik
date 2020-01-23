<?php
    function getTechZones($path){
        if (file_exists($path)){
        }
        else{
        }
    }
    
    function getChannels($path){
        $channels = [];

        class channel{
            public $name = '';
            public $units = '';
            public $scaleL = '';
            public $scaleH = '';

            function __construct($name, $units, $scaleL, $scaleH){
                $this->name = $name;
                $this->units = $units;
                $this->scaleL = $scaleL;
                $this->scaleH = $scaleH;
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
            //если архивация = 1
            if((int)substr($string, strpos($string, '<') + 1, 1)){
                list($name, , $units, $scaleL, $scaleH, , $arch) = preg_split('@[\\\/(\,)<#]@', $string);
                array_push($channels, new channel($name, $units, $scaleL, $scaleH));
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
                    return $num >= 0;
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
        $pointCount = (filesize($path)-$size_serviceData)/($SIZE_VALUE*$file_channelCount)-1;
        $interval = 86400/$pointCount;
        $shift = $SIZE_VALUE*$file_channelCount;

        $timeB = strtotime(date('Y-m-d', filemtime($path)-1).' 00:00:00');

        fseek($fileHandler, $size_serviceData);
            for ($pointNum=0; $pointNum<$pointCount; $pointNum++){
                $point = [($timeB + $interval * $pointNum) * 1000];
                foreach($channels as $key => $channel){
                    fseek($fileHandler, $size_serviceData + $shift * $pointNum + $SIZE_VALUE * $channel);
                    $value = round(unpack('f*', fread($fileHandler, $SIZE_VALUE), SEEK_SET)[1], 3);
                    array_push($point, $value);
                }
                array_push($channelData, $point);
            }
            
        fclose($fileHandler);
        return $channelData;
    }
?>