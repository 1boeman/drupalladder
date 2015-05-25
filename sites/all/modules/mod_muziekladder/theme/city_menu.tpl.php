<?php 

?>
<ul class="front-city-menu clearfix">
<?php foreach ($cities as $city): ?>

  <li class="city city-<?php echo $city['Id']?>" data-id="<?php echo $city['Id']?>"> 
    <a href="<?php echo Muziek_util::city_link($city); ?>">
      <img alt="<?php echo htmlspecialchars($city['Name']); ?>" src="/<?php echo drupal_get_path('theme',$GLOBALS['theme']).'/city_images/' . $city['Id']?>.jpg" />
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
