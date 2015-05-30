<div class="map-placeholder"></div>

<div class="page-uitgaan">
  <div class="club-container" itemscope itemtype="http://schema.org/Place"  id="<?php echo $venue['Id'] ?>">
 
  <?php if (stristr($lang_prefix,'/en/') && strlen($venue['Desc_en'])): ?>   
    
  <p class="description"><?php echo $venue['Desc_en']?></p>		
  
  <?php else: ?>   
   <p class="description"><?php echo $venue['Desc']?></p>		
  
  <?php endif; ?>

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

    <p><strong>Website: </strong> <a href="<?php echo $venue['Link'] ?>"><?php echo $venue['Link'] ?></a><br>
      
  </div>
<h3><?php echo $venue['Title'] ?> <?php echo t('calendar') ?>:</h3>

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
			<a itemprop="url" href="<?php echo $internal_link ?>" class="eventlink">
				<span itemprop="name"><?php echo $event['Title'] ?></span>
			</a>
		</div>
<?php endforeach; ?>
	  <p><a href="<?php echo $venue['Link'] ?>"><?php echo $venue['Link'] ?></a></p>
  
</div>
