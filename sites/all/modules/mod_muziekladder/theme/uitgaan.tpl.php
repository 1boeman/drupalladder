<?php if ($tpl == 'index'): ?>

<h2><?php echo t('Choose a city:')?></h2>

<input id="city_autocomplete" type="text" class="input-xxlarge" placeholder="<?php echo t('City')?>" />
  <p><em><?php echo t('Type a <strong>city name</strong> or click a city on the <strong>map</strong> to browse its <strong>nightlife, venues, festivals and clubs</strong>: ')?></em></p>
 
<div class="stedencontainer clearfix">
  <h2><?php echo t('Alfabetic list of cities') ?></h2>
  <p><?php echo t('Find venues, festivals and clubs in the following cities: ')?></p>
  <?php 
    $breakpoint = count($cities)/3;
    $i=0;
  ?>
  <ul class="steden">
<?php foreach ($cities as $city): ?> 
   <li><a href="<?php echo $lang_prefix ?>uitgaan/<?php echo $city['Id'] ?>-<?php echo rawurlencode($city['Name'])?>" 
      data-cityno="<?php echo $city['Id'] ?>" 
      data-countryno="<?php echo $city['Countryno'] ?>"
      title="<?php echo htmlspecialchars($city['Name']) ?>"
    ><?php echo $city['Name'] ?></a></li>

<?php
  $i++;  
  
  if ($i > $breakpoint) {
    $i=0;   
    echo '</ul> <ul class="steden">';
  }

endforeach; ?>
  </ul>

</div>

<div class="map-placeholder"></div>


<?php endif; ?>


<?php if ($tpl == 'city_main'): ?>
<div class="city-container">
  <?php 
  if (count($nodes)): 
    foreach ($nodes as $node) :
      $output = node_view($node,'full'); 
?>
        <div class="node-container">
          <?php echo drupal_render($output); ?>
        </div>
<?php
    endforeach; 
?>

 <?php endif; ?>
   <div class="map-placeholder"></div>
</div>
  
<div class="locatie-lijst-container clearfix">
  <h2><?php echo t('Venues in ') ?> <?php echo $city['Name'] ?> </h2>
  <div class="formContainer">
    <a href="#add" class="btn btn-inverse handleMe" data-handler="show_venuetips" ><?php echo t('Add venue in').' '.$city['Name'] ?>?</a>
    <div class="venue_tipformContainer">  
        <?php echo drupal_render($venue_form); ?>
    </div>
  </div>
  <ul class="locaties-lijst">
  <?php 
    if (count ($venues)):
      $break = ceil(count($venues)/3);
      $i = 0;
      foreach( $venues as $venue ): ?>
          
    <li data-id="<?php echo $venue['Id'] ?>" class="locatiebeschrijving"> 
      <h4><a class="locatie-link" href="<?php echo $lang_prefix ?>locaties/<?php echo rawurlencode($venue['Id']).'-'.$city['Name'] ?>" 
    title="<?php echo $venue['Title'] ?>"><?php echo $venue['Title']?></a></h4>
      <div class="desc"><p>
          <?php 
          if(stristr($lang_prefix,'/en/')){
            echo  strlen($venue['Desc_en']) ? $venue['Desc_en']  : $venue['Desc'] ;
          } else {
            echo  strlen($venue['Desc']) ? $venue['Desc']  :'';
          }
        ?></p>
      </div>
      <div class="adres">
        <span class="straat"><?php echo $venue['Street']?></span> 
        <span class="straatnummer"><?php echo $venue['Street_number'] ?></span> 
        <span class="straatnummer_toevoeging"> <?php echo $venue['Addition'] ?></span>
      <br>
        <span class="zip"> <?php echo $venue['Zip'] ?></span>
        <strong class="stad"><?php echo $city['Name']?></strong>
      </div>
    </li>
    
  <?php 
    $i++; 
    if ($i > $break){
        $i=0; 
        echo '</ul><ul class="locaties-lijst">';
    }
    endforeach; 
  endif; 

  ?>
  </ul>
</div>

<?php endif; ?>
