<? require('../options/header.php');
require_once (PATH.'/class/func.class.php');
	require_once(PATH.'/class/Mobile_Detect.php');
	require_once(PATH.'/class/browser.class.php');
	require_once(PATH.'/class/order.class.php');
	$func_class=new Func(); //echo __DIR__." = ".PATH;
	$order_class=new Order();
	$gstatus="";
if (isset($_POST['gstatus']) AND ($_POST['gstatus']!='all')) $gstatus.="AND `status`='{$_POST['gstatus']}'";
if ($_POST['tm']!="") $gstatus.="AND `mng_id`='{$_POST['tm']}'";


if ($_POST['dates']!="")
{
  $dates=explode(" - ", $_POST['dates']);
  $begin=date("Y-m-d H:i:s",strtotime($dates['0']));
  $end=date("Y-m-d H:i",strtotime($dates['1'])).":59";
  $gstatus.=" AND `orders_ua`.`datetime`>=\"{$begin}\" AND `orders_ua`.`datetime`<=\"{$end}\"";
} 

if ($_POST['cp']!="")
{
	$gstatus.=" AND `country`=\"{$_POST['cp']}\"";
}

if ($_POST['search']!="")
{
	if (mb_substr(trim($_POST['search']),0,1)=='#') {
		$search=mb_substr($_POST['search'], 1);
		$gstatus.=" AND  (`id_num` LIKE '%{$search}%') ";
	} else {
		$search="%".preg_replace("/([a-z0-9])/i", "$1%", $_POST['search']);
		$gstatus.=" AND ( (`phone` LIKE '%{$search}%') OR (`name` LIKE '%{$_POST['search']}%') ) ";
	}
	
}
?>



<? //$db="SELECT * FROM `orders_ua` INNER JOIN `delivery` ON (`orders_ua`.`delivery` = `delivery`.`id_dlv`) INNER JOIN `managers` ON (`orders_ua`.`mng` = `managers`.`idm`) INNER JOIN `status` ON (`orders_ua`.`status` = `status`.`status_id`) LEFT JOIN `clients` ON (`orders_ua`.`mng_id` = `clients`.`idc`) WHERE `clients`.`id_bot`='{$_GET['idt']}' {$b} {$e} ORDER BY `orders_ua`.`id_num` ASC";
	 $db="SELECT * FROM `orders_ua` INNER JOIN `delivery` ON (`orders_ua`.`delivery` = `delivery`.`id_dlv`) INNER JOIN `managers` ON (`orders_ua`.`mng` = `managers`.`idm`) INNER JOIN `status` ON (`orders_ua`.`status` = `status`.`status_id`) LEFT JOIN `clients` ON (`orders_ua`.`mng_id` = `clients`.`idc`) WHERE `clients`.`id_bot`!='' {$gstatus} {$b} {$e} ORDER BY `orders_ua`.`datetime` DESC";
	//echo $db;
	 $result = mysql_query($db);
$myrow = mysql_fetch_array($result);
do
{ 
	if ($myrow['id_num']!="") {?>

		<tr id="<?= $myrow['id_num'] ?>">
		<? $order_class->OneOrder($myrow,$_POST['gstatus']); ?>
	</tr>
<?
		
	 } else { ?>

		<tr>
			<td colspan="7"><center>Заявок в этом статусе нет</center><br>
				<?// $db ?></td>
		</tr>

	<? }

}
while ($myrow = mysql_fetch_array($result)); ?>