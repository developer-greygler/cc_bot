<?php
    set_time_limit(0);
	require_once (__DIR__.'/../config.php');
	
	date_default_timezone_set(TIME_ZONE);
	require_once(PATH.'/class/db.class.php');
  //require_once('../class/mysql.php');
  
  $db_class=new Db;

$db_class->connect_db(DB_HOST, DB_NAME, DB_LOGIN, DB_PASS);
	
	require_once (PATH.'/class/tbot.class.php');
	require_once (PATH.'/class/func.class.php');

	require_once(PATH.'/class/Mobile_Detect.php');
	require_once(PATH.'/class/browser.class.php');
	require_once(PATH.'/class/notification.class.php');

	$tbot_class=new tbot;
	


	require_once (PATH.'/tbot/function.php');
	require_once (PATH.'/tbot/keyboard.php');
	//require_once ('bot_answer.php');
	//require_once ('bot_full.php');
	//require_once ('bot.php');
	//require_once ('bot_key.php');
