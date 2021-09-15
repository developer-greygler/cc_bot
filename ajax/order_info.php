<div class="col-sm-3">
		
		<ul class="fa-ul">
  				<li><i class="fa-li fa fa-calendar-check-o"></i><?= $date ?>
				 
			</li>
  				<li><i class="fa-li fa fa-clock-o"></i><?= $time ?>
			
			</li>
  			
  				<li><i class="fa-li fa fa-check-square"></i><b><?= $myrow['Nick_Name'] ?></b></li>
  				<? // if ($_POST['gstatus']=='all') { ?>
  				<li><i class="fa-li fa fa-check-square"></i><u class="text-<?= $myrow['status_color'] ?>"><b><?= $myrow['status_name'] ?></b></u></li> <? // } ?>
  			</ul>

                  </div>
                  <div class="col-sm-3">
                  <ul class="fa-ul">
  				<li><i class="fa-li fa fa-user"></i><b><?= $myrow['name'] ?></b></li>
  				<li><i class="fa-li fa fa-phone-square"></i>
				 
					<a href="tel:<?= $myrow['phone'] ?>">
					<?= $myrow['phone'] ?>
				</a>
					
				
				</li>
  				<li><i class="fa-li fa fa-home"></i>
  					<i class="flag-<?= $geo['country_code'] ?>"></i> <small><?= "{$geo['city_name']},<br>{$geo['region_name']}"  ?> <?= $gz ?></small></li>
  			</ul>
  		</div>
          <input type="hidden" name="order_id" value="<?= $myrow['id_num'] ?>">