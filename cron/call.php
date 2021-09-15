<?php

include('head.php');

  
$hd=date("H"); $h=(int) $hd; if (($h>9) AND ($h<21)) {




$i=0;

$db="SELECT * FROM `orders_ua` INNER JOIN `status` ON (`status`.`status_id`=`orders_ua`.`status`) INNER JOIN `delivery` ON (`orders_ua`.`delivery` = `delivery`.`id_dlv`)  INNER JOIN `managers` ON (`orders_ua`.`mng` = `managers`.`idm`) LEFT JOIN `clients` ON (`orders_ua`.`mng_id` = `clients`.`idc`) WHERE  `status`.`theend`=0 AND `orders_ua`.`dt_status`<=".(time()-(60*60)); // `country`='UA' AND

$result = mysql_query($db);
$myrow = mysql_fetch_array($result);
do
{
	if ($myrow['id_num']!="") {
		if ($myrow['dt_status']!=0) $dt_status=$myrow['dt_status']; else $dt_status=strtotime($myrow['datetime']);
		//if ($dt_status <= (time()-(10*60))) {
			$mng_info="🏪 Трафик-менеджер <b>{$myrow['Nick_Name']}</b> :\n\n";
			$tbot_class->del_message($myrow['telegram'], $myrow['tlg_id']);
			if (date('d/m',$dt_status)==date('d/m')) $daten="Сегодня, ".date('H:i',$dt_status); else 
			$daten=date('d/m H:i',$dt_status);
			$info=$tbot_class->send_bot_method('sendmessage', $myrow['telegram'], $mng_info.order_message($myrow, '☎', "Перезвоните! (<b>{$myrow['status_name']}</b>, <i>".$daten."</i>)"), json_encode(pz_key($myrow['id_num']))); 		// json_encode(keyp($myrow['id_num'])));
			print_r(pzf_key($myrow['id_num'],$myrow['phone']));
			print_r($info);
			$results = mysql_query("UPDATE `orders_ua` SET `tlg_id`='{$info['result']['message_id']}' WHERE `id_num`={$myrow['id_num']}");
	$i++; //}
	}
}
while ($myrow = mysql_fetch_array($result));
echo("Итого: {$i}<br>{$db}");

} else {
	echo("Время не рабочее, {$h} час");
}