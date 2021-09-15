<? include('head.php');
	require_once(PATH.'/class/func.class.php');
	
	require_once(PATH.'/push/autoload.php');
	require_once(PATH.'/push/pusher.php');

    $func_class=new func();

$curl = curl_init(); 
curl_setopt($curl, CURLOPT_URL, 'https://api.monsterleads.pro/method/order.list?api_key=6d781e71c11c374e5dde052cf3e035cf&format=json');
//curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$out = curl_exec($curl);
curl_close($curl);
 //print_r($out);
$crm_out=json_decode($out,true);

?><pre><?




 $orders=array(); $otext=array(); $o_count=0;
foreach ($crm_out as $key => $value) {
    echo("{$key}: ");
    print_r($value);
    $db="SELECT * FROM `orders_ua` WHERE `out_crm_id`='{$value['order_key']}'";
   echo("<br><br>{$db}<br><br>");
    $result = mysql_query($db);  $myrow = mysql_fetch_array($result);

    $crm_info=json_decode($myrow['out_crm'],true); 

    echo ($crm_info['logs']." == ".$value['logs'])."<br>";

    if ($crm_info['logs']!=$value['logs']) { echo("Не равно {$value['order_key']}<br>");
        $o_count++; $out_crm_info="#{$value['status_num']}, {$value['status']} - {$value['logs']}";
        if (isset($orders[$value['logs']])) {
            $orders[$value['logs']]++;
        } else {
            $orders[$value['logs']]=1;
            $otext[$value['logs']]=$out_crm_info;
        }
    $out_crm=json_encode($value, JSON_UNESCAPED_UNICODE);
    $results = mysql_query("UPDATE `orders_ua` SET `out_crm`='{$out_crm}', `out_crm_info`='{$out_crm_info}' WHERE `out_crm_id`='{$value['order_key']}'");
    }
} 



if ($o_count>0) {


$mess="🧮 MonsterLeads: \n\n"; $alert="<ul>";
foreach ($orders as $key => $value) {
  //  $mess.="▫ ".$func_class->crop_str($otext[$key])." : $value\n";
    $mess.="▫ {$otext[$key]}: {$value}\n";
    $alert.="<li>".$func_class->crop_str($otext[$key],23)." : {$value}</li>\n";
}
$alert.="</ul>";
$push['message'] = $alert;
$push['title'] = "Информация по статусам LeadMonsters:";
// $push['idu_i']=$idu_i;
// $push['idu_j']=$idu_j;
$push['alert']=true;
//if ($alert) { 
    print_r($push);
echo $pusher->trigger('SVbot', 'update_order_np', $push); 


 $tout=tbot::send_bot_method('sendmessage', $define_chats['DE'], $mess,''); 

}