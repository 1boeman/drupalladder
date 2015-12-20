<div class="nav-dag-outer">
  <div class="navigation clearfix navigation-dagoverzicht-top">
     <div class="nav-dag-inner">
       <nav class="cities-agendas">
        <select class="agenda_city_selecter">
          <option value="0">-- <?php echo t('All cities') ?> --</option>
          <?php foreach($cities as $city): ?>
          <option value="<?php echo $city['Id'].'-'.$city['Name'] ?>" <?php

            if ($cityno == $city['Id']) {
              echo ' selected ';
            }
        ?>><?php echo $city['Name'] ?></option>
          <?php endforeach; ?>
        </select>
      </nav>
      <nav class="prevnextlinks">
       <div class="startdatum-container">
        <span class="label-el"><?php echo t('Starting from')?>:</span>
        <select class="agenda-date-selecter">
          <option value="0"> -- <?php echo t('Date') ?> -- </option>
          <?php foreach($dates as $key=>$value): ?>
            <option value="<?php echo $key ?>" <?php if ($day == $key) echo ' selected '; ?>><?php echo $value ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="daybutton-container">
      <?php if ($dayprev !=0): ?>
          <button class="btn today" data-href="<?php echo $tday ?>" style="display: inline-block;"><a href="<?php echo $tday ?>"><i class="icon-home"></i>
          <?php echo t('Today') ?></a></button>
      <?php endif; ?>
       <?php if ($daynext>1): ?>
          <button class="btn prevday" data-href="<?php echo $prev ?>" style="display: inline-block;"><a href="<?php echo $prev ?>"><i class="icon-backward "></i>
          <?php echo t('Previous day') ?></a></button>
       <?php endif; ?>
       <?php if ( $daynext < 90):?>
         <button class="btn nextday" data-href="<?php echo $next ?>" style="display: inline-block;"><a href="<?php echo $next ?>"><?php echo t('Next day')?> <i class="icon-forward "></i></a></button>
       <?php endif ?>
          </div>
      </nav>
    </div>
  </div>
</div>
<?php
  echo $city_menu;
?>
<a class="btn btn-inverse agenda-add-event" href="<?php echo $add_event ?>"><i class="icon-white icon-plus"></i> <?php echo t('Add event')?></a>
