<? require('../options/header.php');
require_once (PATH.'/class/func.class.php');
	require_once(PATH.'/class/Mobile_Detect.php');
	require_once(PATH.'/class/browser.class.php');
	require_once(PATH.'/class/order.class.php');
	require_once(PATH.'/class/tbot.class.php');
    $tbot_class=new tbot();
    require('order_db.php');
    $order_class=new Order();
    //require(PATH."/tbot/function.php");
   // require(PATH."/tbot/keyboard.php");

    $order_class->OneOrder($myrow,$_POST['gstatus']);

