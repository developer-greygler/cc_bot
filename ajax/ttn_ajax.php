<? require('../options/header.php');
require_once (PATH.'/class/func.class.php');
	require_once(PATH.'/class/Mobile_Detect.php');
	require_once(PATH.'/class/browser.class.php');
	require_once(PATH.'/class/tbot.class.php');
    require_once(PATH.'/push/autoload.php');
    require_once(PATH.'/push/pusher.php');
	


    $tbot_class=new tbot();
    require('order_db.php');

    require(PATH."/tbot/function.php");

    $mess="🏪 Трафик-менеджер <b>{$myrow['Nick_Name']}</b> :\n\n".order_message($myrow, '⛔', 'Редактируется в админке',  DESCRIPTION, 1);


    
  

    $data = array(
        'chat_id' => $define_chats[$myrow['country']],
        'parse_mode' => 'html',
        //'reply_markup' => $mess['reply_markup'],
        'text' => $mess,
        'message_id' => $myrow['tlg_id'],
        //'sticker' => $mess['sticker']
    );

    $tinfo = $tbot_class->send_bot_full('editMessageText', $data);
    if ($tinfo['ok'] == 1) $bot_info = "<span style=\"color:green\">Сообщение в боте обновлено успешно!</span>";
    else $bot_info = "<span style=\"color:red\">Ошибка обновления в боте, \n" . print_r($tinfo, true) . "\n</span>";



  // print_r($myrow); ?>
  <div class="container">
      <div class="row">
      <? require('order_info.php') ?>
          <div class="col-sm-6">
              <div class="form-group">
              <div class="col-sm-6">
                  <select name="delivery" id="delivery" class="form-control">
                  <? $results = mysql_query("SELECT * FROM `delivery` WHERE `country_delivery`='{$myrow['country']}' ORDER BY `delivery`.`id_dlv` ASC");
		            $myrows = mysql_fetch_array($results);
                    do
                    { ?>
                    <option <? if ($myrows['id_dlv']==$myrow['delivery']) echo('selected'); ?> value="<?= $myrows['id_dlv']; ?>"> <?= $myrows['delivery_name'] ?> <? if ($myrows['id_dlv']==$myrow['delivery'])  echo('&#10004;'); ?> </option>
                    <? } while ($myrows = mysql_fetch_array($results)); ?>
                  </select>
              </div>
              </div>

              <div class="form-group">
              <div class="col-sm-6">

              <input type="text" name="ttn" id="ttn" placeholder="TTH заявки" value="<?= $myrow['ttn']; ?>" class="form-control">

              </div>
              </div>

          </div>
       </div>
       <?= $bot_info ?>
      </div>
  </div>

  <? $push['order_id']=$_POST['recipient'];
	
	$push['button']=$_POST['button'];
	$push['message'] = " для изменения информации о доставке";
	
  $push['title'] = "Открыт заказ #{$_POST['recipient']}:";
   $push['alert']=true;
   
  $pusher->trigger('SVbot', 'open_order', $push); 
 