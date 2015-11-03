
<div class="event_in_node">
  <?php if (!(int)$summary): ?>
  <div class="user_in_node">
    <a class="user_link_in_node" data-uid="<?php echo $tip['uid'] ?>"  href="<?php echo Muziek_util::lang_url().'user/'.$tip['uid']  ?>"><?php echo $tip['user_name'] ?></a>
  </div>
  <?php endif; ?>
  <ul class="event_data_in_node">
    <li class="event_link_in_node"><a href="<?php echo $tip['link'] ?>" target="_blank"><?php echo $tip['link'] ?></a></li>
    <li class="venue_in_node"><?php 
        if ( $tip['venue_select']){
          if ($tip['db_venue']) {
          ?><a href="<?php echo $tip['locatie_link']?>"><?php echo $tip['db_venue']['Title'] ?></a> <?php 
          } else { 
            echo $tip['venue_select'];
          }   
        } else {?>
          <div class="freetext_in_node">
          <?php echo nl2br(filter_xss($tip['venue_freetext']));?>
          </div>
        <?php
        }
      ?>
    </li>
    <li class="city_in_node"><span><?php 
      if ($tip['city_select'] && isset($tip['db_city']) && $tip['db_city']){
        echo $tip['db_city']['Name'] ;
      } elseif (isset($tip['city'])) {
         echo filter_xss($tip['city']);
      } else{
        echo t('unknown'); 
      } 
    ?></span>
    </li>
    <li class="dates_in_node">
      <span><?php echo t('Date') ?>:</span>
      <ul>
      <?php foreach( $tip['timestamp_array'] as $key => $value ): ?>
        <li><?php echo t(date('l',$key)) ?> <?php echo date('j',$key) ?> <?php echo t(date('F',$key)).', '.date('Y',$key); ?>
        </li>
      <?php endforeach ?>
      </ul>
    </li>
 
  <?php if (!$summary) : ?> 
    <li class="soort_in_node"><?php echo $tip['soort'] ?></li>
  <?php endif; ?>

  </ul>
  <?php if (!$summary) : ?> 
  <p class="description_in_node">
    <?php echo nl2br(filter_xss($tip['description'])); ?>
  </p>
  <?php endif; ?>
</div>  
