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

$trackarray=array (
 
 //"DateTimeFrom" => date("d.m.Y", time()-(60*60*24)),
 //"DateTimeTo" => date("d.m.Y"),
 // 'DateTime' => '2021-09-11',
    "GetFullList" => "1",
   // "RedeliveryMoney"=> "1"
);

$out=$np_class->list_ttn($trackarray);
$np=json_decode($out,true);


$mess_chat="🔮 Синнхронизация ТТН Новой почты:\n";
$mess_count=array();


if ($np['success']==1) {

  ?> <pre><? print_r($trackarray); ?></pre><br><hr><br><pre><? // print_r($np); ?></pre>
  <?

  echo("Всего : {$np['info']['totalCount']}<br>"); 
  $i=1; $ct=1; $mess_count['Всего']=$np['info']['totalCount'];


 $dbd = "DELETE FROM `control_ttn` WHERE `datetime` >".date("Y-m-d H:i:s", time()-time()-(60*60*24*3)) ;// 2021-09-14 16:35:24
 $resultd = mysql_query($dbd);


    foreach ($np['data'] as $key => $value) {

        echo("{$ct}. ");

        $dbt="SELECT * FROM `control_ttn` WHERE `ttn`={$value['IntDocNumber']}"; // 
        $resultt = mysql_query($dbt);

        if(mysql_num_rows($resultt) < 1 ) {

        $tout = $tbot_class->send_bot_method('sendmessage',$define_delivery_chats['NP'],  "📮 ТТН {$value['IntDocNumber']}\n📞 {$value['RecipientsPhone']}\n👤 {$value['RecipientContactPerson']}\n📋 {$value['AdditionalInformation']}");

        $dbi="INSERT INTO `control_ttn`(`ttn`, `date_ttn`) VALUES ('{$value['IntDocNumber']}','{$value['DateTime']}')";
        $resulti = mysql_query($dbi);
      //  echo ($dbi);
        $phone="%".preg_replace("/([a-z0-9])/i", "$1%", $value['RecipientsPhone']);
        
        $db="SELECT * FROM `orders_ua` WHERE (`status`<>6) AND (`phone` LIKE '{$phone}')"; // 
        $result = mysql_query($db);

        if(mysql_num_rows($result) > 0 ) {
            
            if (mysql_num_rows($result) == 1) {
                $myrow = mysql_fetch_array($result);
                $order_id=$myrow['id_num']; echo("ТТН: {$myrow['ttn']} = ");
                if (($myrow['ttn']=="") OR ($myrow['ttn']=='*'))
                {
                    echo ("TTH Пустая? заполняем! ");
                    if (isset($mess_count['Добавлено'])) $mess_count['Добавлено']++; else $mess_count['Добавлено']=1;
                    $search  = array("'");
			        $replace = array("\'");

			        $json = str_replace($search, $replace, json_encode($value, JSON_UNESCAPED_UNICODE));

                    $dbu = "UPDATE `orders_ua` SET `name`='{$value['RecipientContactPerson']}', `delivery_response_start` = '{$json}', `ttn` = '{$value['IntDocNumber']}' WHERE `id_num` = '{$order_id}'";
				    $resultu = mysql_query($dbu);
                    echo($dbu);

                    if ($resultu == 'true') { echo "Информация в базе обновлена успешно!"; } else {  echo "Ошибка #".mysql_errno() . ": " . mysql_error() . ", { $dbu} "; }

               // echo $dbu."<br><br>";

                } else {
                    if (isset($mess_count['Уже есть'])) $mess_count['Уже есть']++; else $mess_count['Уже есть']=1;
                }


            } else {
                echo ('Найдено несколько !!!');
                if (isset($mess_count['Найдено несколько '.$value['RecipientsPhone']])) $mess_count['Найдено несколько '.$value['RecipientsPhone']]++; else $mess_count['Найдено несколько '.$value['RecipientsPhone']]=1;
            }
       
        } else {

            echo("Такого ТТН {$value['IntDocNumber']}, {$value['RecipientsPhone']} : {$value['RecipientContactPerson']},  {$value['AdditionalInformation']} не нашел!");
            if (isset($mess_count['Телемагазин'])) $mess_count['Телемагазин']++; else $mess_count['Телемагазин']=1;
        }
        echo(" : {$db}<br>");

        $i++;

    } else {
        echo("Сообщение ТТН {$value['IntDocNumber']} уже отправлено<br>");
    }
    $ct++;
    }


    if ($i>1) {
    $push_np = "<ul>";
    foreach ($mess_count as $key => $value) {

        $mess_chat.="\n▫ {$key} : {$value}";
        $push_np .= "<li> {$key} : {$value}</li>";
    
    } 
    $push_np .= "</ul>";



$push['message'] = $push_np;
	$push['title'] = "Синнхронизация ТТН Новой почты:";

    $push['alert'] = true;
	//if ($alert) { 
	echo $pusher->trigger('SVbot', 'update_order_np', $push);
    $tout = $tbot_class->send_bot_method('sendmessage',$define_chats['UA'],  $mess_chat);
    
} else {
    echo("Писать не чего)");
}

}
else {
    $mess_chat.="Какая-то ошибка: ".print_r($np,true);
    echo("Какая-то ошибка: ".print_r($np,true));
}