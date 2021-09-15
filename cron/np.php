<?php

include('head.php');
require_once(PATH . '/class/np.class.php');
require_once(PATH . '/class/func.class.php');
require_once(PATH . '/options/np_emoji.php');
require_once(PATH . '/options/status_emoji.php');
require_once(PATH . '/push/autoload.php');
require_once(PATH . '/push/pusher.php');

$func_class = new func();
$np_class = new Np();

// $db="SELECT * FROM `orders_ua` INNER JOIN `status` ON (`status`.`status_id`=`orders_ua`.`status`) INNER JOIN `delivery` ON (`orders_ua`.`delivery` = `delivery`.`id_dlv`)  INNER JOIN `managers` ON (`orders_ua`.`mng` = `managers`.`idm`) LEFT JOIN `clients` ON (`orders_ua`.`mng_id` = `clients`.`idc`) WHERE `status`.`status_id`=1 AND `orders_ua`.`ttn`<>'' AND `orders_ua`.`ttn`<>'*'";

$db = "`orders_ua` INNER JOIN `status` ON (`status`.`status_id`=`orders_ua`.`status`) INNER JOIN `delivery` ON (`orders_ua`.`delivery` = `delivery`.`id_dlv`)  INNER JOIN `managers` ON (`orders_ua`.`mng` = `managers`.`idm`) LEFT JOIN `clients` ON (`orders_ua`.`mng_id` = `clients`.`idc`)";

$where = " ((`orders_ua`.`status`=1) OR (`orders_ua`.`status`=8)) AND `orders_ua`.`ttn`<>'' AND `orders_ua`.`ttn`<>'*'";
// $where=" `datetime` > '".date("Y-m-d",time()-60*60*24*90)." 00:00:00' ((`orders_ua`.`status`=1) OR (`orders_ua`.`status`=8)) AND `orders_ua`.`ttn`<>'' AND `orders_ua`.`ttn`<>'*'";
//$where=" `orders`.`ttn`<>'' AND `orders`.`delivery`=1";



$count = $db_class->cound_bd_big($db, $where, 1);
echo ("Найдено {$count} шт.<br>\n");

$ceil = ceil($count / 100);
echo ("Проходов: {$ceil}<br>\n");
$begin = 0;

$status = array();
$ttn = array();
$order = array();
$nps = array();
$npsc = array();
for ($i = 0; $i < $ceil; $i++) {

	echo ("Проход: {$i}<br><br>\n\n");

	$document = array();

	$dbs = "SELECT * FROM {$db} WHERE {$where}  ORDER BY `orders_ua`.`id_num` ASC LIMIT {$begin}, 99";
	echo ($dbs . "<br><br>\n\n");
	$result = mysql_query($dbs);
	$myrow = mysql_fetch_array($result);
	do {


		$document[] = array(
			"DocumentNumber" => $myrow['ttn'],
			"Phone" => preg_replace('![^0-9]+!', '', $myrow['phone'])
		);
		$ttn[$myrow['id_num']] = $myrow['ttn'];
		$status[$myrow['id_num']] = $myrow['status'];

		//echo("{$myrow['id_num']}: TTN:{$myrow['ttn']}, status:{$myrow['status_name']}<br><br>\n\n");

	} while ($myrow = mysql_fetch_array($result)); // от здесь

	$otvet = $np_class->tracing($document);

	//echo("<pre>");print_r($otvet);echo("</pre>");

	$response = json_decode($otvet, true);

	// echo("<pre>");print_r($response);echo("</pre>");

	if (count($response['data']) == 0) {
		$text = "Какие-то проблемы с АПИ Новой Почты";
		//$tout=file_get_contents("https://api.telegram.org/bot".TELEGRAM_BOT."/sendmessage?chat_id=".BOSS."&parse_mode=html&text={$text}");
		$tout = $tbot_class->send_bot(BOSS, $text);
	}
	if ($response['success'] == 1) {
		echo ("Status array:<br>\n");
		print_r($status);
		echo ("<br><br>\n\n"); // до тут

		foreach ($response['data'] as $key => $value) {

			$new_status = $np_class->np_status($value['StatusCode']);

			if (($value['DatePayedKeeping'] != "") and (($value['StatusCode']==7) OR ($value['StatusCode']==8)) and ((strtotime($value['DatePayedKeeping']) - time()) < (60 * 60 * 20))) {
				$new_status = 8; //55
				// СТАТУС ПЛАТНОГО ХРАНЕНИЯ!!!!!
				$vstatus = "Платное хранение";
				$vstatuscode = 999;
			} else {
				$vstatus = $value['Status'];
				$vstatuscode = $value['StatusCode'];
			}


			if ($new_status != 0) {
				$status_db = ", `status` = {$new_status}";

				$id = array_search($value['Number'], $ttn);
				echo ("{$id} => '{$value['Number']}', Старый статус: '{$status[$id]}', Новый Статус: '{$new_status}'<br>\n");


				// вот тут обновление статусов в боте
				// $id - номер заказа

				// конец обновления статусов и пуш


				if ($status[$id] != $new_status) {
					$nps[$new_status] = $vstatus;
					$npsc[$new_status] = $vstatuscode;
					if (isset($order[$new_status])) $order[$new_status]++;
					else $order[$new_status] = 1;
				}
			} else {
				$status_db = "";
			}





			 if ($value['Status']!="Номер не знайдено") {

			// echo("Value:<br>\n"); print_r($value); echo("<br>\n");

			foreach ($value as $keyv => $valuev) {
				$searchv  = array('"');
				$replacev = array('\"');
				$value[$keyv] = str_replace($searchv, $replacev, $valuev);
			}

			$search  = array("'");
			$replace = array("\'");

			//$json=json_encode($value, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT);
			$json = str_replace($search, $replace, json_encode($value, JSON_UNESCAPED_UNICODE));
			// $json=json_encode($value);

			if ($value['LastAmountTransferGM'] != '') $ttn_summ = $value['LastAmountTransferGM'];
			else  $ttn_summ = $value['RedeliverySum'];



			$db = "UPDATE `orders_ua` SET `delivery_response` = '{$json}', `ttn_status`='" . str_replace($search, $replace, $value['Status']) . "', `ttn_summ`='{$ttn_summ}' {$status_db} WHERE `orders_ua`.`ttn` = '{$value['Number']}'";
			//$db="UPDATE `orders_ua` SET `delivery_response` = '{$json}'  WHERE `orders_ua`.`ttn` = '{$value['Number']}'";
			echo ("{$key} ({$id}): {$db}<br><br>\n\n");
			$result = mysql_query($db);
			if ($result == 'true') {
				echo "<span style=\"color:green\">Информация в базе обновлена успешно!</span>";

				$resultottn = mysql_query("SELECT * FROM `orders_ua` INNER JOIN `status` ON (`orders_ua`.`status` = `status`.`status_id`) LEFT JOIN  `delivery` on (`orders_ua`.`delivery` = `delivery`.`id_dlv`) INNER JOIN `clients` ON (`orders_ua`.`mng_id` = `clients`.`idc`) WHERE `ttn` LIKE '{$value['Number']}'");
				$order_ttn = mysql_fetch_array($resultottn);

				//echo("<br>");print_r($order_ttn);echo("<br>");

				echo("{$order_ttn['tlg_id']} : {$order_ttn['tlg_id']} | {$status[$id]} = {$new_status}<br>");

				if (($order_ttn['tlg_id'] != "") and ($order_ttn['tlg_id'] != "0") AND ($status[$id]!=$new_status)) {
				
					$mess="🏪 Трафик-менеджер <b>{$order_ttn['Nick_Name']}</b> :\n\n".order_message($order_ttn, $status_emoji[$order_ttn['status']], $order_ttn['status_name'],  DESCRIPTION, 1);



					$data = array(
						'chat_id' => $define_chats['UA'],
						'parse_mode' => 'html',
						//'reply_markup' => $mess['reply_markup'],
						'text' => $mess,
						'message_id' => $order_ttn['tlg_id'],
						//'sticker' => $mess['sticker']
					);

					$tinfo = $tbot_class->send_bot_full('editMessageText', $data);
					if ($tinfo['ok'] == 1) echo "<span style=\"color:green\">Сообщение в боте обновлено успешно!</span>";
					else echo "<span style=\"color:red\">Ошибка обновления в боте, \n" . print_r($tinfo, true) . "\n</span>";
				} else {
					echo "<span style=\"color:blue\">Сообщение в бот не отправлялось!</span>";
				}
			} else {
				echo "<span style=\"color:red\">Ошибка: " . mysql_errno() . ": " . mysql_error() . "</span>";
			}

			echo ("<br><br><br><br>\n\n\n\n");
		} else {
			echo("<span style=\"color: brown\">{$value['Status']}</span><br>");
		}
		} // foreach response


	} else {
		echo ("<pre>");
		print_r($response);
		echo ("</pre>");
	}


?>
	<pre><? // print_r(json_decode($otvet,true));
			?></pre><?

															$begin = $begin + 100;
														}


														$text = "🚚 <b>Новая Почта</b> - обновление статусов:\n\n";
														$stnp = 0;
															?>
Итого:<br>
<? $push_np = "<ul>";
foreach ($nps as $key => $value) {
	echo ("{$value} : {$order[$key]}<br>\n");
	$text .= "{$np_emoji[$npsc[$key]]} " . $func_class->crop_str($value, 23) . " : <b>{$order[$key]}</b> ед.\n";
	$stnp++;
	$push_np .= "<li> {$value} : {$order[$key]}</li>";
}
$push_np .= "</ul>";
if ($stnp > 0) { // 



	$push['message'] = $push_np;
	$push['title'] = "Информация по статусам Новой Почты:";
	// $push['idu_i']=$idu_i;
	// $push['idu_j']=$idu_j;
	$push['alert'] = true;
	//if ($alert) { 
	echo $pusher->trigger('SVbot', 'update_order_np', $push);
	//}

	//if (isset($_GET['tbot'])) $tbot=$_GET['tbot']; else $tbot=BOSS;



	// $tout=tbot::send_bot($tbot, $text);

	$tout = $tbot_class->send_bot_method('sendmessage', $tbot, $text, '');
	//$tout=file_get_contents("https://api.telegram.org/bot".TELEGRAM_BOT."/sendmessage?chat_id=".BOSS."&parse_mode=html&text={$text}");
} else {
	$mess = "🚚 Данные по Новой Почте без изменений";
	//if (isset($_GET['tbot'])) {
	//	$tbot = $_GET['tbot'];
		$tout = $tbot_class->send_bot_method('sendmessage',$define_chats['UA'], $mess);
//	}
}
