<? include('../options/header.php');

// 2021-08-29 09:02:31

$count=array(); $dbs=""; $dbt="";


if ($_POST['cp']!="")
{
	$dbt.=" AND `country`=\"{$_POST['cp']}\"";
}

if ($_POST['dates']!="")
{
  $dates=explode(" - ", $_POST['dates']);
  $begin=date("Y-m-d H:i:s",strtotime($dates['0']));
  $end=date("Y-m-d H:i",strtotime($dates['1'])).":59";
  $dbt.=" AND `orders_ua`.`datetime`>=\"{$begin}\" AND `orders_ua`.`datetime`<=\"{$end}\"";
}

if ($_POST['tm']!="") $dbs.=" AND `orders_ua`.`mng_id`='{$_POST['tm']}'";
$result = mysql_query("SELECT * FROM `status` WHERE `status_name`<>'' AND `status_id`<>'9'  ORDER BY `status_id` ASC");
		$myrow = mysql_fetch_array($result);
do
{ 
$count['stat'][$myrow['status_id']]=$db_class->cound_bd('orders_ua', "`orders_ua`.`status`='{$myrow['status_id']}' {$dbs} {$dbt}");
 } while ($myrow = mysql_fetch_array($result));
$count['stat']['all']=$db_class->cound_bd('orders_ua', "`status`<>'9' {$dbs} {$dbt}");
$count['stat']['summ']=$db_class->summ('orders_ua', "ttn_summ","WHERE `status`=10 {$dbs} {$dbt}");
$count['stat']['summp']=$db_class->summ('orders_ua', "ttn_summ","WHERE `status`=8 {$dbs} {$dbt}");

$result = mysql_query("SELECT * FROM `clients` WHERE `c_active` = 1 ORDER BY `idc` ASC");
    $myrow = mysql_fetch_array($result);
do
{ 
$count['tm'][$myrow['idc']]=$db_class->cound_bd('orders_ua', "( (`orders_ua`.`status`='1') OR (`orders_ua`.`status`='8') OR (`orders_ua`.`status`='10') OR (`orders_ua`.`status`='11') ) AND `orders_ua`.`mng_id`='{$myrow['idc']}' {$dbt}"); // Всего
$count['tm1'][$myrow['idc']]=$db_class->cound_bd('orders_ua', "`orders_ua`.`status`='1' AND `orders_ua`.`mng_id`='{$myrow['idc']}' {$dbt}"); // Принято
$count['tm10'][$myrow['idc']]=$db_class->cound_bd('orders_ua', "`orders_ua`.`status`='10' AND `orders_ua`.`mng_id`='{$myrow['idc']}' {$dbt}"); // Принято
$count['tm8'][$myrow['idc']]=$db_class->cound_bd('orders_ua', "`orders_ua`.`status`='8' AND `orders_ua`.`mng_id`='{$myrow['idc']}' {$dbt}"); // Принято
$count['tm11'][$myrow['idc']]=$db_class->cound_bd('orders_ua', "`orders_ua`.`status`='11' AND `orders_ua`.`mng_id`='{$myrow['idc']}' {$dbt}"); // Принято
//echo $db_class->cound_bd('orders_ua', "`orders_ua`.`status`='10' AND `orders_ua`.`mng_id`='{$myrow['idc']}' {$dbt}",1);
 } while ($myrow = mysql_fetch_array($result));


echo(json_encode($count));
  ?>