<?php
    $techZone = $_GET['techZone'];
    $date = $_GET['date'];

    $path_techZoneDir = $_SERVER['DOCUMENT_ROOT'] . '/data/' . $techZone;
    $date_number = str_replace('-', '', $date);
    
    if($handle = opendir($path_techZoneDir)){

        //пропуск '.' и '..'
        readdir($handle);
        readdir($handle);

        if($firtst_entry = readdir($handle)){
            rewinddir($handle);
            
            //пропуск '.' и '..'
            readdir($handle);
            readdir($handle);

            $pre_entry = '';
            while($entry = readdir($handle)){
                if(str_replace('-', '', $entry) <= $date_number){
                    $pre_entry = $entry;
                    continue;
                }
                break;
            }
            if($pre_entry != ''){
                $php_response = $path_techZoneDir . '/' . $pre_entry;
            }
            else{
                $php_response = [false, 'Ошибка запроса рабочей папки. Для тех. зоны ' . $techZone . ' отсутствуют данные до даты ' . $firtst_entry . '.'];
            }
        }
        else{
            $php_response = [false, 'Ошибка запроса рабочей папки. Для тех. зоны ' . $techZone . ' вовсе отсутствуют какие-либо данные.'];
        }

        closedir($handle);
        echo json_encode($php_response);
    }
    else{
        echo json_encode([false, 'Ошибка запроса рабочей папки. Отсутствует директория запрашивающейся тех. зоны ' . $techZone . '.']);
    }
?>