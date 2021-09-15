<?php
require_once ('head.php');
require_once(PATH . '/class/np.class.php');
require_once(PATH . '/class/func.class.php');
//require_once(PATH . '/options/np_emoji.php');
//require_once(PATH . '/options/status_emoji.php');
//require_once(PATH . '/push/autoload.php');
//require_once(PATH . '/push/pusher.php');

$func_class = new func();
$np_class = new Np();

$mess="🚚 <b>Новая Почта</b> - обновление отделений:\n\n";

$trackarray=array ();

$out=$np_class->list_np($trackarray);
$array=json_decode($out,true);

if ($array['success']==1) {

    $kvo=count($array['data']);
    
    echo "Найдено {$kvo} отделений НП.<br>";
    $mess.="🧾 Найдено <b>{$kvo}</b> отделений НП.\n";

    
    $db_main="INSERT INTO `novaposhta`(`SiteKey`, `Description`, `DescriptionRu`, `ShortAddress`, `ShortAddressRu`, `Phone`, `TypeOfWarehouse`, `Ref`, `Number`, `CityRef`, `CityDescription`, `CityDescriptionRu`, `SettlementRef`, `SettlementDescription`, `SettlementAreaDescription`, `SettlementRegionsDescription`, `SettlementTypeDescription`, `SettlementTypeDescriptionRu`) VALUES ";
     
    $c=0; $db1=$db_main;
    
    foreach ($array['data'] as $key => $value) {
    
        foreach ($value as $keyv => $valuev) {
            $searchv  = array("'",'"');
            $replacev = array("\'",'\"');
            if ((($valuev=="") OR ($valuev=="\\")) AND ($keyv=='ShortAddressRu'))   { $value['ShortAddressRu']=$value['ShortAddress']; }
                else { $value[$keyv] = str_replace($searchv, $replacev, $valuev); }
        }
    
        $db1.="\n('{$value['SiteKey']}','{$value['Description']}','{$value['DescriptionRu']}','{$value['ShortAddress']}','{$value['ShortAddressRu']}','{$value['Phone']}','{$value['TypeOfWarehouse']}','{$value['Ref']}','{$value['Number']}','{$value['CityRef']}','{$value['CityDescription']}','{$value['CityDescriptionRu']}','{$value['SettlementRef']}','{$value['SettlementDescription']}','{$value['SettlementAreaDescription']}','{$value['SettlementRegionsDescription']}','{$value['SettlementTypeDescription']}','{$value['SettlementTypeDescriptionRu']}'),"; 
    
    
    
        if (($key % 950 ) == 0) { $db[$c] = substr($db1, 0, -1);   $c++; $db1=$db_main;  }
    
    }

    if ($db1!="") { $db[$c] = substr($db1, 0, -1);    }
    


    $result = mysql_query ("TRUNCATE `novaposhta`");
    $error_count=0; $error=array();
    foreach ($db as $key => $value) {
        $result = mysql_query ($value);
        echo ("'{$key}', ");
        if ($result != 'true') {  echo mysql_errno() . ": " . mysql_error() . $value."<br><br><br>"; $error_count++;
            $error[]="Ошибка: #".mysql_errno() . ": <b>" . mysql_error()."</b>";
        }

    }

      echo "Все!<br>";


      
// Изучаем ошибки
if ($error_count>0){
    $mess.="⚠ Обнаружено {$error_count} ошибок обновления БД:\n";
    foreach ($error as $key => $value) {
        $mess.="{$value}\n";
        }
    } else { 
     $count_city=$db_class->count_bd('novaposhta');
     $mess.="🧮 В базе обновлено <b>{$count_city}</b> записей,\n";
    
     if ($count_city==$kvo) {
        $mess.="✔ Все в порядке!";
     } else {
        $mess.="⚠ Где-то закралась неточность!";
        echo ("Нет в базе: ");
        foreach ($array['data'] as $key => $value) {


            if ($db_class->count_bd('novaposhta',"`CityID`={$value['CityID']}")==0)
            {
                echo ("{$value['Description']} ({$value['DescriptionRu']})<br>");
            }

           
        }
    }

}

} else {  $mess.="❗ Ошибка запроса АПИ Новой Почты!"; }

echo $mess;

$tout = $tbot_class->send_bot_method('sendmessage', $define_chats['UA'], $mess);
    
    
     
    