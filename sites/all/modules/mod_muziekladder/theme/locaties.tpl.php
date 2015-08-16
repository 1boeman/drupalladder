<?php  if (!isset($not_found)):  ?>
 
  <div class="page-uitgaan<?php if ( $diverse_locaties ){ echo ' diverse-locaties'; }?> ">
    <div class="club-container" itemscope itemtype="http://schema.org/Place"  id="<?php echo $venue['Id'] ?>">
   
    <?php if (stristr($lang_prefix,'/en/') && strlen($venue['Desc_en'])): ?>   
      
    <p class="description"><?php echo $venue['Desc_en']?></p>		
    
    <?php else: ?>   
     <p class="description"><?php echo $venue['Desc']?></p>		
    
    <?php endif; ?>
    <div class="map-placeholder"></div>
 
      <strong itemprop="name" class="locatie-titel"><?php echo $venue['Title'] ?></strong>
      <strong> - <?php echo $venue['City_name'] ?></strong>

      <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
          <div itemprop="streetAddress">
            <span class="straatnaam"><?php echo $venue['Street'] ?> </span>  
            <span class="straatnummer"><?php echo $venue['Street_number']?> </span> 
            <span class="straatnummer-toevoeging" ><?php echo $venue['Addition']?></span> 
          </div>
          <span itemprop="postalCode"><?php echo $venue['Zip'] ?> </span> 
          <span itemprop="addressLocality" class="city city-<?php echo $venue['Cityno'] ?>" data-cityno="<?php echo $venue['Cityno'] ?>"><?php echo $venue['City_name']?> </span> 
          
         <div class="country-name"><?php echo t($venue['Country_name'])?></div>
      </div>			
      <p><strong>Website: </strong> <a href="<?php echo $venue['Link'] ?>"><?php echo $venue['Link'] ?></a><br></p>
        
    </div>
    <?php $blockclass = ( count($events) > 1 ) ? ' locatie-half' : ''; ?>
    <div class="aux-block clearfix">
      <div id="kolovoz" class="locatie-agenda-container<?php echo $blockclass ?>">
        <div class="locatie-half-inner">
          <h2><?php echo $venue['Title'] ?> <?php echo t('calendar') ?>:</h2>

          <?php foreach ($events as $event): 
            $internal_link = Muziek_util::gig_link($event); 
            $timestamp = strtotime($event['Date']);
            $monthname = t(date("F",$timestamp));
            $dayname = t(date("l",$timestamp));
            $daynumber =  date('d',$timestamp); 
            $human_date = $dayname. ' ' .$daynumber.' '.$monthname. ' ' .date('Y',$timestamp); ?>
          
            <div class="event clearfix" itemscope itemtype="http://schema.org/Event">
              <a itemprop="url" href="<?php echo $internal_link ?>">
                <strong itemprop="startDate" class="date"><?php echo ucfirst($human_date) ?></strong>
              </a> : 
              <a itemprop="url" href="<?php echo $internal_link ?>" class="eventlink"><span itemprop="name"><?php echo $event['Title'] ?></span>
              </a>
            </div>
        <?php endforeach; ?>
            <?php echo $comment_form; ?>

            <p class="venue_link"><a href="<?php echo $venue['Link'] ?>"><?php echo $venue['Link'] ?></a></p>
           </div>
        </div>
        <div class="review-node-container<?php echo $blockclass ?>">
            <div class="locatie-half-inner">
              <?php echo $venue_node; ?>
            </div>
        </div>
    </div>
  </div>
<?php else: ?>
  <div>
    <?php if (stristr($lang_prefix,'/en/')): ?>   
      <p><em>Sorry...</em> at the moment there is no information available for the location you've requested.</p>
      <p><a href="/en/uitgaan">Check out some of the other locations available to find something of your interest.</a></p>
    <?php else: ?>
      <p><em>Sorry...</em> op dit moment is er geen informatie beschikbaar over de locatie die je hebt opgevraagd.</p>
      <p><a href="/nl/uitgaan">Wellicht vindt je meer interessante locaties op de locatie pagina.</a></p>
    <?php endif; ?>
  </div>
<?php endif; ?>
