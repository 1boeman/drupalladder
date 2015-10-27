<?php 
if (isset($simple_list)): 
  if (!isset($_GET['pagina'])|| $_GET['pagina']=='0'):?>
  <nav>
    <ul class="nav nav-pills agenda-city-menu">
      <?php foreach ($cities as $city): ?>
      <li><a href="<?php echo Muziek_util::city_link($city); ?>"><?php echo $city['Name'] ?></a></li>
      <?php endforeach; ?>
    </ul>
  </nav> 
<?php
  endif;  
else:
?>
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
