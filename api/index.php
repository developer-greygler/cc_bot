<? 
header('Access-Control-Allow-Origin: *');
$postData = file_get_contents('php://input');
//if ($postData!=""){
$today=time();
$data_array = json_decode($postData, true);
$logfile = fopen("log.txt", "a+");
fwrite($logfile, print_r($data,true)."\n\n");
fclose($logfile);
$get = explode("/", $_SERVER['REQUEST_URI']);
require_once('../config.php');
// date_default_timezone_set(TIMEZONE);
if (version_compare(PHP_VERSION, '7.0.0','>=')) include '../class/mysql.php';
require_once('../class/db.class.php');

require_once('../class/func.class.php');
require_once('../class/Mobile_Detect.php');
require_once('../class/browser.class.php');

require_once('../push/autoload.php');
	require_once('../push/pusher.php');

require_once('../class/tbot.class.php');
include("../options/tabgeo_country_v4.php");
include("../options/country_emoji.php");
require_once ('../tbot/keyboard.php');
	$server=$data_array['server'];
	

	$tbot=new tbot();
	$db_class=new Db();
	$func_class=new Func();
	$detect = new Mobile_Detect($server, $server['HTTP_USER_AGENT']);

	$browser_class = new Browser($server['HTTP_USER_AGENT']);

$db_class->connect_db(DB_HOST, DB_NAME, DB_LOGIN, DB_PASS);
// require_once('../define.php');

//$data_array['ChatID']
//$data_array['managerName']

// ЧАТ МЕНЕДЖЕРОВ ПО ОБЗВОНУ:

if ((isset($data_array['site'])) AND ($data_array['site']!="")) $site= $data_array['site'];
else {
		
	$site=$func_class->scheme($server)."://".str_ireplace("www.", "", $server['HTTP_HOST']).str_ireplace(basename($server['PHP_SELF']),'', $server['PHP_SELF']);
}

if ((isset($data_array['country'])) AND ($data_array['country']!="")) $country=$data_array['country']; else $country='UA';

$result = mysql_query("SELECT `idm` FROM `managers` WHERE `telegram` = '{$data_array['ChatID']}'");
  if(mysql_num_rows($result) > 0 ) {  
 			
 			$results = mysql_query("SELECT * FROM `managers` WHERE `telegram` = '{$data_array['ChatID']}'");
			$myrow = mysql_fetch_array($results);
			$manager_id=$myrow['idm'];
			echo "Менеджер #{$manager_id}  {$data_array['managerName']} ({$data_array['ChatID']}) уже есть";

		} else { 
			$result = mysql_query("INSERT INTO `managers`(`name_chat`, `telegram`) VALUES ('{$data_array['managerName']}','{$data_array['ChatID']}')"); 

				if ($result == 'true')
				{
				
				$results = mysql_query("SELECT * FROM  `managers` ORDER BY  `managers`.`idm` DESC LIMIT 1");
				$myrow = mysql_fetch_array($results);
				$manager_id=$myrow['idm'];

				echo "Менеджер #{$manager_id} {$data_array['managerName']} ({$data_array['ChatID']}) добавлен успешно!";

				}
				else
				{
				  echo "Ошибка #" . mysql_errno() . ": " . mysql_error() . " ";
				}
		}

		echo("<br>");

// Личный чат трафик-менеджера

$result = mysql_query("SELECT `idc` FROM `clients` WHERE `id_bot` = '{$data_array['managerID']}'");
  if(mysql_num_rows($result) > 0 ) {  
 			
 			$results = mysql_query("SELECT * FROM `clients` WHERE `id_bot` = '{$data_array['managerID']}'");
			$myrow = mysql_fetch_array($results);
			$telegram_manager_id=$myrow['idc'];
			echo "Трафик-менеджер #{$telegram_manager_id}  {$data_array['managerName']} ({$data_array['managerID']}) уже есть<br>\n";
			$resultu = mysql_query("UPDATE `clients` SET `Nick_Name` = '{$data_array['managerName']}' WHERE `clients`.`idc` = {$telegram_manager_id}");

		} else { 
			$result = mysql_query("INSERT INTO `clients`(`Nick_Name`, `id_bot`) VALUES ('{$data_array['managerName']}','{$data_array['managerID']}')"); 

				if ($result == 'true')
				{
				
				$results = mysql_query("SELECT * FROM  `clients` ORDER BY  `clients`.`idс` DESC LIMIT 1");
				$myrow = mysql_fetch_array($results);
				$telegram_manager_id=$myrow['idc'];

				echo "Менеджер #{$telegram_manager_id} {$data_array['managerName']} ({$data_array['managerID']}) добавлен успешно!<br>\n";

				}
				else
				{
				  echo "Ошибка #" . mysql_errno() . ": " . mysql_error() . " <br>\n";
				}
		}


		$result = mysql_query("SELECT `idp` FROM `products` WHERE `url` = '{$site}'");
		if(mysql_num_rows($result) > 0 ) {  
				   
				 
				  $resultu = mysql_query("UPDATE `products` SET `id_mng`='{$telegram_manager_id}',`name_product`='{$data_array['product']}',`price`='{$data_array['price']}',`country`='{$country}' WHERE `url`='{$site}'");
	  
			  } else { 
				  $resulti = mysql_query("INSERT INTO `products`
				  ( `id_mng`, `name_product`, `price`, `url`, `country`) VALUES 
				  ('{$telegram_manager_id}','{$data_array['product']}','{$data_array['price']}','{$site}','{$country}')");
	  
					  if ($resulti == 'true')
					  {
						$resultn = mysql_query("SELECT `idp` FROM  `products` ORDER BY  `products`.`idp` DESC LIMIT 1");
						$lidp = mysql_fetch_array( $resultn );
 
					  
	  
					  echo "Товар #{$lidp['0']} {$data_array['product']}  добавлен успешно!<br>\n";
	  
					  }
					  else
					  {
						echo "Ошибка #" . mysql_errno() . ": " . mysql_error() . " <br>\n";
					  }
			  }

		echo("<br>");

//if ((isset($data['oid'])) AND ($data['oid']!=0) AND ($data['oid']!="")) $oid=$data['oid']; else $oid=1;
if ($get['2']!="") { $api_block=$get['2'].'.php';
if (file_exists($api_block)) require($api_block);
else require('docs.php'); } else require('docs.php');
//} else { 	echo('JSON Error!'); }
