<?

$server=$data_array['server'];
$server_json=base64_encode(json_encode($server, JSON_UNESCAPED_UNICODE));
$ip=$func_class->GetRealIp($server);

$lang=$func_class->lang($server);
$host=@gethostbyaddr($ip);
$date=$func_class->date_rus_time_min($today);
$time=date("H:i:s",$today);

$geo = json_decode($func_class->geo_it($ip), true);

$utm_json=base64_encode(json_encode($data_array['utm'], JSON_UNESCAPED_UNICODE));

$db="INSERT INTO `orders_ae`	( `mng`, `mng_id`,  `name`, `phone`, `email`, `product`, `price`,`comment`, `url`, `server`, `status`, `UTM`, `referer`, `delivery`, `ttn`) 	VALUES 	('{$manager_id}','{$telegram_manager_id}','{$data_array['bayer_name']}','{$data_array['phone']}','{$data_array['email']}','{$data_array['product']}','{$data_array['price']}','{$data_array['comment']}','{$data_array['site']}','{$server_json}','0','{$utm_json}','{$data_array['ref']}','1','')"; // По умолчанию - Новая Почта

echo("<br>{$db}<br>");

$result = mysql_query($db);

if ($result == 'true')
				{
					$results = mysql_query("SELECT * FROM  `orders_ae` ORDER BY  `orders_ae`.`id_num` DESC LIMIT 1");
					$myrow = mysql_fetch_array($results);
					echo("Заявка {$myrow['id_num']} - успех<br>\n");
					//$id_num
				} 

				else
				{
				  echo "Ошибка #" . mysql_errno() . ": " . mysql_error() . " <br>\n";
				}



$deviceType = ($detect->isMobile($server['HTTP_USER_AGENT']) ? ($detect->isTablet($server['HTTP_USER_AGENT']) ? '📋 <b>Устройство</b>: Tablet' : '📱 <b>Устройство</b>: Phone') : '💻 <b>Устройство</b>: Computer');



$browser=$browser_class->getBrowser($server['HTTP_USER_AGENT'])." v.".$browser_class->getVersion($server['HTTP_USER_AGENT']);

$os=$browser_class->getPlatform($server['HTTP_USER_AGENT']);

//$scheme=$func_class->scheme($server);
//$host=$domen.$host_path;
//$server="{$scheme}://{$host}";

if ($data_array['device']!="Desktop") $geo_text=$geo['as']; else $geo_text="{$geo['city_name']} {$geo['region_name']} {$geo['country_name']}";

  if ($data_array['mess']!="")   $new_order=stripslashes(htmlspecialchars($data_array['mess'])); else $new_order="Новый заказ";
  $mess="🆕 <b>{$new_order}</b> #{$myrow['id_num']}:\n";


  // $chatId=$data_array['id']; //!!!!!!
  //if ($data_array['host']!="") $host=$data_array['host']; else
  //    if ($data_array['ip']!="")$host=@gethostbyaddr($data_array['ip']);



  if ($data_array['product']!="") $mess.="🛒 <b>Продукт</b>: ".stripslashes(htmlspecialchars($data_array['product']))."\n";
  if ($data_array['price']!="") $mess.="💸 <b>Цена</b>: ".stripslashes(htmlspecialchars($data_array['price']))."\n";
  if ($data_array['site']!="") $mess.="🔗 <b>Сайт</b>: ".stripslashes(htmlspecialchars($data_array['site']))."\n";
  $mess.="🗓 <b>Дата заказа</b>: {$date}\n"; 	
  $mess.="⏰ <b>Время заказа</b>: {$time} (<i>".TIME_ZONE."</i>)\n"; 	
  $mess.="🧭 <b>Таймзона</b>: {$geo['time_zone']} (по IP)\n";
  if ($data_array['bayer_name']!="") $mess.="👤 <b>Имя</b>: ".stripslashes(htmlspecialchars($data_array['bayer_name']))."\n";
  if ($data_array['phone']!="") $mess.="☎ <b>Телефон</b>: ".stripslashes(htmlspecialchars($data_array['phone']))."\n";
  if ($data_array['email']!="") $mess.="📧 <b>E-mail</b>: ".stripslashes(htmlspecialchars($data_array['email']))."\n";
	if ($data_array['comment']!="") $mess.="🗨 <b>Комментарий</b>: ".stripslashes(htmlspecialchars($data_array['comment']))."\n";
	if ($geo['country_code']!="")  $country_code = tabgeo_country_v4($ip); else $country_code = $geo['country_code'];

  $mess.="{$country_emoji[$country_code]} <b>IP</b>: {$ip} ({$country_code})\n📡 <b>Источник</b>: {$host}\n📎 <b>Хост</b>: {$geo['as']}\n🏙 <b>Город (по IP)</b>: {$geo['city_name']}, {$geo['region_name']}\n";
 

  if ($data_array['longtime']!="") $mess.="⏱ <b>Время на сайте</b>: ".stripslashes(htmlspecialchars($data_array['longtime']))."\n";
  //if ($data_array['visit']!="") $mess.="📄 <b>Посещений</b>: ".stripslashes(htmlspecialchars($data_array['visit']))."\n";
  //if ($data_array['lastvisit']!="") $mess.="👁‍🗨 <b>Последнее посещение</b>: ".stripslashes(htmlspecialchars($data_array['lastvisit']))."\n";
  
  // if ($data_array['crm']!="") $mess.="⚙ ".$data_array['crm']."\n"; 
   	$mess.="{$deviceType}\n";
 		$mess.="🧮 <b>ОС</b>: {$os}\n";
  	$mess.="📺 <b>Браузер</b>: {$browser}\n";
  	$mess.="🗣 <b>Язык</b>: {$lang}\n";
  if ($data_array['screen']!="") $mess.="🖥 <b>Экран</b>: ".stripslashes(htmlspecialchars($data_array['screen']))."\n";


  if (($data_array['utm']!=""))  $mess.="🧶 <b>UTM-метки</b>"; $ui=0;

  foreach ($data_array['utm'] as $key => $value) {
  	if ($value!="") {
  		$mess.="\n🔸 <b>{$key}</b>: ".stripslashes(htmlspecialchars($value));
  		$ui++;
  	}
  }

  if ($ui>0) $mess.="n"; else $mess.=" Не обнаружены\n";
  
  if ($data_array['ref']!="") $mess.="🖇 <b>Реферер</b>: {$data_array['ref']}\n";



/*

$keyboard = array(
				"inline_keyboard" => array(

					array(
						
						array(
							"text" => "👎 НО",
							'callback_data' => 'NO '.$myrow['id_num']
					),
						array(
							'text' => '🐐 ВНЗ', 
							'callback_data' => 'VNZ '.$myrow['id_num']

					),


					),
					array(
						array(
							'text' => '🚽 СПАМ', 
							'callback_data' => 'SPAM '.$myrow['id_num']

					),
						array(
							'text' => '🛠 TЕСТ', 
							'callback_data' => 'TEST '.$myrow['id_num']

					),
					

				),
					array(
						
						array(
							"text" => "✅ ПРИНЯТО",
							'callback_data' => 'ORDERS_OK '.$myrow['id_num']
					),
						
					

				)

				),

				"one_time_keyboard" => TRUE, // можно заменить на FALSE,клавиатура скроется после нажатия кнопки автоматически при True
				"resize_keyboard" => TRUE // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
				);
*/

$keyboard =  pz_key($myrow['id_num']);

/* $keyboard = array(
				"inline_keyboard" => array(

					
					
					array(
						
						array(
							"text" => "📞 Я ПРИНИМАЮ ЗАЯВКУ",
							'callback_data' => 'OrdersIam '.$myrow['id_num']
					),
						
					

				)

				),

				"one_time_keyboard" => TRUE, // можно заменить на FALSE,клавиатура скроется после нажатия кнопки автоматически при True
				"resize_keyboard" => TRUE // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
				); */

$mng_info="🏪 Трафик-менеджер <b>{$data_array['managerName']}</b> :\n\n";

$info=$tbot->send_bot_method('sendmessage', $data_array['ChatID'], $mng_info.$mess,json_encode($keyboard));

echo("Ответ1: <br>");
print_r($info);
echo("<br>----------<br>");

if ((isset($data_array['managerID'])) AND ($data_array['managerID']!="")) {
		$info2=$tbot->send_bot_method('sendmessage', $data_array['managerID'], $mess,''); 
		$db2=", `tlg_id_m`='{$info2['result']['message_id']}'";
	} else { $db2=""; }


echo("Ответ2: <br>");
print_r($info2);
echo("<br>----------<br>");

$results = mysql_query("UPDATE `orders_ae` SET `tlg_id`='{$info['result']['message_id']}' {$db2} WHERE `id_num`={$myrow['id_num']}");



