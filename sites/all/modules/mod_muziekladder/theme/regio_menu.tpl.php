<?php $lang_url = Muziek_util::lang_url();
 $agenda_day =  $day ? '/agenda-'.$day.'.html' :'/' 
?>
<?php if (isset($simple_list)): ?>

<nav class="regio-menu">
  <ul class="nav nav-pills agenda-city-menu">
    <?php foreach($regios as $regio): ?>
    <li class="<?php 
      echo $regio['Id'];
      if ($current_regio && $current_regio==$regio['Id']){ print ' active'; }?>">
      <a href="<?php echo $lang_url.'muziek/regio-'.$regio['Id'].$agenda_day ?>"><?php echo t($regio['Name']) ?></a>
    </li>
    <?php endforeach; ?>
  </ul>
</nav>

<?php else:?>
<nav>
<ul class="front-city-menu clearfix">
<?php foreach ($cities as $city): ?>
  <li class="city city-<?php echo $city['Id']?>" data-id="<?php echo $city['Id']?>"> 
    <a href="<?php echo Muziek_util::city_link($city); ?>">
      <span class="city-name"><?php echo $city['Name']?></span>
    </a>

    <ul class="city-links">
      <li>
        <a href="<?php echo Muziek_util::city_link($city); ?>"><?php echo t('Calendar')?></a> |
        <a href="<?php echo Muziek_util::city_link($city,'uitgaan'); ?>"><?php echo  t('Locations') ?></a></li>
    </ul>

  </li>

<?php endforeach; ?>
</ul>
</nav>

<?php endif; ?>
