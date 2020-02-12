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
            //если архивация = 1
            if((int)substr($string, strpos($string, '<') + 1, 1)){
                $data = [];
                $separators = ['\\', '/', '(', ',', ')', '<', '#'];
                $firstCharPos = 0;
                foreach($separators as $separator){
                    $lastCharPos = strpos($string, $separator);
                    $sliceLength = $lastCharPos - $firstCharPos;
                    array_push($data, substr($string, $firstCharPos, $sliceLength));
                    $firstCharPos = $lastCharPos + 1;
                }
                list($name, , $units, $scaleL, $scaleH) = $data;

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

    function parseArhFile($path, $channelIds, $firstSecond, $lastSecond){
        $channelData = [];

        $SECONDS_PER_DAY = 86400; //60s*60m*24h

        $BYTES_PER_CHANNEL_COUNT = 2;
        $BYTES_PER_DATE = 16;
        $BYTES_PER_CONF = 61;
        $BYTES_PER_VALUE = 4;

        $EMPTY_VALUE_CODE = 11111;
        
        $fileHandler = fopen($path, 'rb');
        $file_channelCount = unpack('S*', fread($fileHandler, $BYTES_PER_CHANNEL_COUNT), 0)[1];
        $bytesPerServiceData = $BYTES_PER_CHANNEL_COUNT + $BYTES_PER_DATE + $BYTES_PER_CONF * $file_channelCount;
        $file_pointCount = (filesize($path) - $bytesPerServiceData)/($BYTES_PER_VALUE * $file_channelCount)-1;  //-1 потому, что в ddmm.arh Value(t=24:00:00)=11111
        $file_pointTimeStep = $SECONDS_PER_DAY/$file_pointCount;
        $bytesPerOneTimePoints = $BYTES_PER_VALUE*$file_channelCount;

        $firstPointNum = floor($firstSecond/$file_pointTimeStep);
        $lastPointNum = ceil($lastSecond/$file_pointTimeStep);
        if ($lastPointNum > $file_pointCount){
            $lastPointNum = $file_pointCount;
        }

        $date = strtotime(date('Y-m-d', filemtime($path)-1).' 00:00:00');

        fseek($fileHandler, $bytesPerServiceData);
        for ($pointNum = $firstPointNum; $pointNum <= $lastPointNum; $pointNum++){
            $momentData = [($date + $file_pointTimeStep * $pointNum)*1000];
            foreach($channelIds as $channelNum => $channelId){
                fseek($fileHandler, $bytesPerServiceData + $bytesPerOneTimePoints * $pointNum + $BYTES_PER_VALUE * $channelId);
                $readedValue = unpack('f*', fread($fileHandler, $BYTES_PER_VALUE), SEEK_SET)[1];
                if($readedValue != $EMPTY_VALUE_CODE){
                    $writedValue = round($readedValue, 3);
                    array_push($momentData, $writedValue);
                }
                /*else{
                    $momentData = [];
                }*/
            }
            if(count($momentData) > 1){
                array_push($channelData, $momentData);
            }
        }
            
        fclose($fileHandler);
        return $channelData;
    }

    function getFileLastModDate($path){
        return filemtime($path);
    }
?>