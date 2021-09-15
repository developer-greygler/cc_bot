<? include('../options/header.php');
$count=array(); $dbs="";
// if ($_POST['tm']!="") $dbs.=" AND `orders_ua`.`mng_id`='{$_POST['tm']}'";
$result = mysql_query("SELECT * FROM `clients` WHERE `c_active` = 1 ORDER BY `idc` ASC");
		$myrow = mysql_fetch_array($result);
do
{ 
$count[$myrow['idc']]=$db_class->cound_bd('orders_ua', "`orders_ua`.`status`='10' AND `orders_ua`.`mng_id`='{$myrow['idc']}' {$dbs}");
 } while ($myrow = mysql_fetch_array($result));

echo(json_encode($count));
  ?>