<?php

include('head.php');
require_once(PATH . '/class/np.class.php');
require_once(PATH . '/class/func.class.php');
//require_once(PATH . '/options/np_emoji.php');
//require_once(PATH . '/options/status_emoji.php');
//require_once(PATH . '/push/autoload.php');
//require_once(PATH . '/push/pusher.php');

$func_class = new func();
$np_class = new Np();

$out=$np_class->np_cityes();
$array=json_decode($out,true);

$mess="🚚 <b>Новая Почта</b> - обновление городов:\n\n";

if ($array['success']==1) {

    $kvo=count($array['data']);

echo "Найдено {$kvo} городов.<br>";
$mess.="🧾 Найдено <b>{$kvo}</b> городов.\n";





$db_main="INSERT INTO `cityes_np` (`Description`, `DescriptionRu`, `Ref`, `Delivery1`, `Delivery2`, `Delivery3`, `Delivery4`, `Delivery5`, `Delivery6`, `Delivery7`, `Area`, `SettlementType`, `IsBranch`, `PreventEntryNewStreetsUser`, `CityID`, `SettlementTypeDescriptionRu`, `SettlementTypeDescription`, `SpecialCashCheck`, `AreaDescription`, `AreaDescriptionRu`) VALUES ";

$c=0; $db1=$db_main; $abc=array(); $region=array();
// print_r($array['data']);

foreach ($array['data'] as $key => $value) {

  //  $value['DescriptionRu']=trim($value['DescriptionRu']);
  if ($value['DescriptionRu']!="") $Description=trim($value['DescriptionRu']); else $Description=trim($value['Description']);
    $e=mb_substr($Description,0,1);
   
    if (isset($abc[$value['AreaDescriptionRu']][$e])) $abc[$value['AreaDescriptionRu']][$e]++; else $abc[$value['AreaDescriptionRu']][$e]=1;
    



    foreach ($value as $keyv => $valuev) {
        $searchv  = array("'");
        $replacev = array("\'");
        $value[$keyv] = str_replace($searchv, $replacev, $valuev);
    }
$db1.="('{$value['Description']}', '{$Description}', '{$value['Ref']}', '{$value['Delivery1']}', '{$value['Delivery2']}', '{$value['Delivery3']}', '{$value['Delivery4']}', '{$value['Delivery5']}', '{$value['Delivery6']}', '{$value['Delivery7']}', '{$value['Area']}', '{$value['SettlementType']}', '{$value['IsBranch']}', '{$value['PreventEntryNewStreetsUser']}', '{$value['CityID']}', '{$value['SettlementTypeDescriptionRu']}', '{$value['SettlementTypeDescription']}', '{$value['SpecialCashCheck']}', '{$value['AreaDescription']}', '{$value['AreaDescriptionRu']}'),"; 


 if (($key % 950 ) == 0) {

    

   $db[$c] = substr($db1, 0, -1); 
    $c++; $db1=$db_main; 


    
     
 }
} 

if ($db1!="") {
    $db[$c] = substr($db1, 0, -1); 
}

$result = mysql_query ("TRUNCATE `cityes_np`");
$error_count=0; $error=array();
foreach ($db as $key => $value) {
  $result = mysql_query ($value);
  echo ("'{$key}', ");
if ($result != 'true') {  echo mysql_errno() . ": " . mysql_error() . $value."<br><br><br>"; ;$error_count++;
    $error[]="Ошибка: #".mysql_errno() . ": <b>" . mysql_error()."</b>";
}
}



ksort($abc,SORT_STRING);
$abcj=json_encode($abc);
file_put_contents(PATH.'/data/abc.json', $abcj);


// Изучаем ошибки
if ($error_count>0){
    $mess.="⚠ Обнаружено {$error_count} ошибок обновления БД:\n";
    foreach ($error as $key => $value) {
        $mess.="{$value}\n";
    }
} else { 
    $count_city=$db_class->count_bd('cityes_np');
    $mess.="🧮 В базе обновлено <b>{$count_city}</b> записей,\n";
    
    if ($count_city==$kvo) {
        $mess.="✔ Все в порядке!";
    } else {
        $mess.="⚠ Где-то закралась неточность!";
        echo ("Нет в базе: ");
        foreach ($array['data'] as $key => $value) {


            if ($db_class->count_bd('cityes_np',"`CityID`={$value['CityID']}")==0)
            {
                echo ("{$value['Description']} ({$value['DescriptionRu']})<br>");
            }

           
        }
    }

}

} else {
    $mess.="❗ Ошибка запроса АПИ Новой Почты!";
}

echo $mess;

$tout = $tbot_class->send_bot_method('sendmessage', $define_chats['UA'], $mess);