<?
class Order {


    public function OneOrder($myrow, $gstatus)
    {
        $func_class=new Func();
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
	
	//$geo_text="{$geo['city_name']} {$geo['region_name']} {$geo['country_name']}";

	?>
	
		<td>
			
			<ul class="list-inline">
  <li><i class="flag-<?= $myrow['country']; ?>"></i></li>
  <li><span  class="badge"><?= $myrow['id_num'] ?></span></li>
</ul>	
		<button style="margin: 5px 15px;" id="edit<?= $myrow['id_num'] ?>" type="button"  data-toggle="modal" data-target="#orderModal" data-type_button="edit" data-whatever="<?= $myrow['id_num'] ?>" class="btn btn-danger btn-order<?= $myrow['id_num'] ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
	
	</td>
		
		<td>
		
		<ul class="fa-ul">
  				<li><i class="fa-li fa fa-calendar-check-o"></i><?= $date ?>
				 
			</li>
  				<li><i class="fa-li fa fa-clock-o"></i><?= $time ?>
				  <button style="margin-top: 10px; position: absolute; right: 1px; z-index: 15" title="Настройка статуса" id="status<?= $myrow['id_num'] ?>" type="button"  data-toggle="modal" data-target="#orderModal" data-type_button="status" data-whatever="<?= $myrow['id_num'] ?>" class="btn btn-primary btn-sm btn-order<?= $myrow['id_num'] ?>"><i class="fa fa-list" aria-hidden="true"></i></button>	
			</li>
  			
  				<li><i class="fa-li fa fa-check-square"></i><b><?= $myrow['Nick_Name'] ?></b></li>
  				<? if ($gstatus=='all') { ?>
  				<li><i class="fa-li fa fa-check-square"></i><u class="text-<?= $myrow['status_color'] ?>"><b><?= $myrow['status_name'] ?></b></u></li> <? } ?>
  			</ul>

  		</td>
		<td><ul class="fa-ul">
  				<li><i class="fa-li fa fa-user"></i><b><?= $myrow['name'] ?></b></li>
  				<li><i class="fa-li fa fa-phone-square"></i>
				  <? if ($myrow['country']=='UA') { ?>
				  <span id="phone<?= $myrow['id_num'] ?>"></span>
				<? } else { ?> 
					<a href="tel:<?= $myrow['phone'] ?>">
					<?= $myrow['phone'] ?>
				</a>
					
					<?} ?>
				</li>
  				<li><i class="fa-li fa fa-home"></i>
  					<i class="flag-<?= $geo['country_code'] ?>"></i> <small><?= "{$geo['city_name']},<br>{$geo['region_name']}"  ?> <?= $gz ?></small></li>
  			</ul>
  		</td>
		<td>
			<ul class="fa-ul">
				<li><i class="fa-li fa fa-external-link"></i>
					<a target="_blank" href="<?= $myrow['url'] ?>"><?= $myrow['url'] ?></a></li>
				<li><i class="fa-li fa fa-file"></i><b><?= $myrow['product'] ?></b></li>
				<li><i class="fa-li fa fa-tag"></i><?= $myrow['price'] ?></li>
				<? if ($myrow['comment']!="") { ?>
				<li><i class="fa-li fa fa-comment"></i><?=  str_replace(array("\r\n", "\r", "\n"), '<br>',$myrow['comment']); ?></li>
				<? } ?>
			</ul>
		</td>
		<td><ul class="fa-ul">
				<li><i class="fa-li fa fa-truck"></i><?= $myrow['delivery_name']; ?>
				<? if ($myrow['delivery_response']!="") { 
							$delivery_response=json_decode($myrow['delivery_response'],true);
							?> <!-- <?  print_r($delivery_response); ?> -->
					<? } //gg ?>

				<button style="margin-top: 55px; position: absolute; right: 1px; z-index: 15" title="Настройка отправки" id="ttn<?= $myrow['id_num'] ?>" type="button"  data-toggle="modal" data-target="#orderModal" data-type_button="ttn" data-whatever="<?= $myrow['id_num'] ?>" class="btn btn-info btn-order<?= $myrow['id_num'] ?> btn-sm"><i class="fa fa-truck" aria-hidden="true"></i></button>

			</li>
				<? if (($myrow['delivery_info']!="") AND ($myrow['ttn']=="")) { ?>
				<li><i class="fa-li fa fa-address-book-o"></i><?= $myrow['delivery_info']; ?></li>

			<? } if (($myrow['ttn']!="") AND ($myrow['ttn']!="*")) { ?>
				<li><i class="fa-li fa fa-paper-plane"></i><?= $myrow['ttn']; ?></li>
				<? } ?>

				<? if ($myrow['delivery_response']!="") { ?>

				<li><i class="fa-li fa fa-map-o"></i>

					<abbr title="<?= $delivery_response['RecipientAddress']; ?>"><?= $delivery_response['CityRecipient']; ?> </abbr>

					</li>


				<li><i class="fa-li fa fa-briefcase"></i> 

				<? if (($delivery_response['DateFirstDayStorage']!="") AND (($delivery_response['StatusCode']=="7") OR ($delivery_response['StatusCode']=="8") ))  // 2021-09-15
					{  $DateFirstDayStorage=$delivery_response['DateFirstDayStorage'];
						if (date("Y-m-d",strtotime('Tomorrow')) == $DateFirstDayStorage) {
							$status_class="warning"; $status_text="Платне зберігання із завтра!";
							$ph_text="";
						} else {
							if (date("Y-m-d",strtotime('Today')) >= $DateFirstDayStorage) 
							{
								$status_class="danger"; $status_text="Платне зберігання!";
								$ph_text="";
							} else {
								$status_class="primary"; $status_text="{$delivery_response['Status']} ".date("d.m.Y H:i",strtotime($delivery_response['ActualDeliveryDate']));
								$ph_text="<li class=\"text-info\"><i class=\"fa fa-li fa-sticky-note\"></i> Платное хранение c ".date("d.m.Y",strtotime($DateFirstDayStorage))." </li>";
							}
						}
						//$DateFirstDayStorage
					} else {

						$status_class="info"; $status_text=$delivery_response['Status'];
								$ph_text="";
					} ?>

				<? if (($gstatus=='10') OR ($gstatus=='all')) { ?>

				<abbr class="text-<?= $status_class; ?>" title="<?= $delivery_response['Status']; ?>"><?= $func_class->crop_str($delivery_response['Status'],23); ?></abbr>
				<? } else { echo  ("<span class=\"text-{$status_class}\">{$status_text}</span>"); } ;  ?>
				
				</li> <?= $ph_text; ?>
				<? if ($delivery_response['AdditionalInformationEW']!="") { ?>
					<li><i class="fa-li fa fa-commenting-o"></i><?= $delivery_response['AdditionalInformationEW']; ?></li>
				<? } ?>


<? }  if ($myrow['ttn_summ']!="0.00") { ?>
<li><i class="fa-li fa fa-money"></i><?= $myrow['ttn_summ']; ?></li>
<? } ?>
</ul>


<!-- <?  // print_r($delivery_response) ?> -->
		</td>
	
		
		
		<td> 
			<ul class="fa-ul">
				<li>
					<i title="Устройство: <?= $deviceType ?>" class="fa-li fa fa-lg fa-<?= $deviceType ?>"></i>
					<i title="ОС: <?= $os ?>" class="os os_<?= mb_strtolower($os);  ?>"></i>&nbsp;&nbsp;
					<i title="Браузер: <?= "{$browser} v.{$br_ver}" ?>" class="br br_<?= mb_strtolower($browser);  ?>"></i>&nbsp;&nbsp;
					<i title="Язык: <?= $lang ?>" class="flag-<?= mb_strtoupper($lang);  ?>"></i>
					<button style="margin-top: 65px; position: absolute; right: 1px; z-index: 15" title="Информация о клиенте" id="serv<?= $myrow['id_num'] ?>" type="button"  data-toggle="modal" data-target="#orderModal" data-type_button="serv" data-whatever="<?= $myrow['id_num'] ?>" class="btn btn-default btn-xs pull-right"><i class="fa fa-server" aria-hidden="true"></i></button>

				</li>  
				<? if (($myrow['longtime']!="") AND ($myrow['longtime']!=0)) { ?>
				<li><i class="fa-li fa fa-tachometer"></i> <? if ($myrow['longtime']>=3600) echo (date("H час", $myrow['longtime'])) ?>  <?= date("i мин s сек", $myrow['longtime']) ?></li>
				<? } ?>
				<li><i class="fa-li fa fa-map-pin"></i><i class="flag-<?= mb_strtoupper($geo['country_code']) ?>"></i> <?= $ip ?></li>
				<li><i class="fa-li fa fa-server"></i><small><?= $host ?></small></li>
				<li><i class="fa-li fa fa-certificate"></i><small><?= $geo['as'] ?></small></li>


			</ul>

			</td>
		<td>
			<ul class="fa-ul">
				
			<? foreach ($utm as $key => $value) { 
				if ($value!="") { ?>
				<li><i class="fa-li fa fa-check-square"></i> <b><?= $key ?></b>: <abbr title="<?= $value ?>"><?= $func_class->crop_str($value, 16)  ?></abbr> </li>
			<? } } 
			if ($myrow['referer']!="") {
			?>
			<li><i class="fa-li fa fa-link"></i><a target="_blank" href="<?= $myrow['referer']; ?>" title="<?= $myrow['referer']; ?>"><?= $func_class->crop_str($myrow['referer'],32); ?></a></li>
			<? } ?>
		</ul>

		
	</td>
	<? if ($myrow['country']=='UA') { ?>
	<script>
		$("#phone<?= $myrow['id_num'] ?>").html(getPhoneTemplateByLocalization( "UA", "<?= preg_replace('![^0-9]+!', '', $myrow['phone']) ?>" ));
	</script>
	<? } ?>
	<td><div class="btn-group" role="group" aria-label="edit">	</div></td>
	 <?
    }



}

