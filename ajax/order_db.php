<?   $db="SELECT * FROM `orders_ua` INNER JOIN `delivery` ON (`orders_ua`.`delivery` = `delivery`.`id_dlv`) INNER JOIN `managers` ON (`orders_ua`.`mng` = `managers`.`idm`) INNER JOIN `status` ON (`orders_ua`.`status` = `status`.`status_id`) LEFT JOIN `clients` ON (`orders_ua`.`mng_id` = `clients`.`idc`)  WHERE `id_num` = {$_POST['recipient']}";
 //  $db="SELECT * FROM `orders_ua` WHERE `id_num` = {$_POST['recipient']}";
	//echo $db;
	 $result = mysql_query($db);
    $myrow = mysql_fetch_array($result);
    $server=json_decode(base64_decode($myrow['server']),true); 
    $detect = new Mobile_Detect($server, $server['HTTP_USER_AGENT']);
	$browser_class = new Browser($server['HTTP_USER_AGENT']);
	$ip=$func_class->GetRealIp($server);
	$lang=$func_class->lang($server);
	$host=@gethostbyaddr($ip);
	$datetime=strtotime($myrow['datetime']);
	if (date("d.m.Y")==date("d.m.Y",$datetime)) $date="Сегодня"; 
	else 	$date=$func_class->date_rus_time_min($datetime);
	$time=date("H:i:s",strtotime($myrow['datetime']));
	$today=$func_class->date_rus_time_min(time())." ".date("H:i:s")." (<i>".TIME_ZONE."</i>)";

	if ($myrow['geo']=='')
	{
		$geo_json = $func_class->geo_it($ip);
		$geo = json_decode($geo_json, true); $gz="<span class=\"text-danger\">*</span>";
		$results = mysql_query("UPDATE `orders_ua` SET `geo`='{$geo_json}'  WHERE `id_num`={$myrow['id_num']}");
	} else
	{
		$geo=json_decode($myrow['geo'],true); $gz="<span class=\"text-success\">*</span>";	}
	
	$utm=json_decode(base64_decode($myrow['UTM']),true);
	$deviceType = ($detect->isMobile($server['HTTP_USER_AGENT']) ? ($detect->isTablet($server['HTTP_USER_AGENT']) ? 'tablet' : 'mobile') : 'laptop');
	$browser=$browser_class->getBrowser($server['HTTP_USER_AGENT']);
	$br_ver=$browser_class->getVersion($server['HTTP_USER_AGENT']);
	$os=$browser_class->getPlatform($server['HTTP_USER_AGENT']);