<? require('../options/header.php');
require_once (PATH.'/class/func.class.php');
	require_once(PATH.'/class/Mobile_Detect.php');
	require_once(PATH.'/class/browser.class.php');
	require_once(PATH.'/class/tbot.class.php');
    $tbot_class=new tbot();
    require('order_db.php');

    require(PATH."/tbot/function.php");
    require(PATH."/tbot/keyboard.php");

    if ((($myrow['status']>=2) AND ($myrow['status']<=12)) AND ($myrow['status']!=10))
    {
       $reply_markup=renew_status($myrow['id_num'],'ua');
    } else {
        if ($myrow['status']==1) {
            $reply_markup=pz_key($myrow['id_num'],'ua');
        } else {
            if ($myrow['status']!=10) {
                $reply_markup=ttn_np($myrow['id_num'],'ua');
            }
            else {
                $reply_markup="";
            }
        }
    }

    $out=array();

    $out['new_status']=$myrow['status'];

   // echo ("Статус: {$myrow['status']}");
   // print_r($reply_markup);

    $mess="🏪 Трафик-менеджер <b>{$myrow['Nick_Name']}</b> :\n\n";

    if (((($myrow['status']>=4) AND ($myrow['status']<=6))) OR ($myrow['status']==12))
    {
        $mess.= "{$status_emoji[$myrow['status']]} Заказ {$country_emoji[$myrow['country']]} #{$myrow['id_num']} «{$myrow['status_name']}»";
    } else {
        
        $mess.= order_message($myrow, $status_emoji[$myrow['status']], $myrow['status_name'],  DESCRIPTION, 1);

    }

   
   

    $data = array(
        'chat_id' => $define_chats[$myrow['country']],
        'parse_mode' => 'html',
        'reply_markup' => json_encode($reply_markup),
        'text' => $mess,
        'message_id' => $myrow['tlg_id'],
        //'sticker' => $mess['sticker']
    );

    $tinfo = $tbot_class->send_bot_full('editMessageText', $data);
    if ($tinfo['ok'] == 1) $out['info'] = "<span style=\"color:green\">Сообщение в боте обновлено успешно!</span>";
    else $out['info'] = "<span style=\"color:red\">Ошибка обновления в боте, \n" . print_r($tinfo, true) . "\n</span>";

    echo (json_encode($out));