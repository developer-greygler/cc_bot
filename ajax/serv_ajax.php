<? require('../options/header.php');
require_once (PATH.'/class/func.class.php');
	require_once(PATH.'/class/Mobile_Detect.php');
	require_once(PATH.'/class/browser.class.php');
	$func_class=new Func(); //echo __DIR__." = ".PATH;
   require('order_db.php'); ?>
  
    <div class="container">
    <dl class="dl-horizontal">
 

    <?
    foreach ($server as $key => $value) { ?>
         <dt><abbr title="<?= $key ?>"><?= $key ?></abbr></dt>  <dd><abbr title="<?= $value ?>"><?= $func_class->crop_str($value,66); ?></abbr></dd>
   <? } ?> </dl></div>

