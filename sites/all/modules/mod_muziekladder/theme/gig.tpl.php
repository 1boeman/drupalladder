<?php if ($gig): ?>
  <nav>
    <ul class="breadcrumb">
      <li><a href="<?php echo $prefix ?>muziek/"><?php echo t('Calendar') ?></a> <span class="divider"><i class="icon-chevron-right"></i></span></li>
      <li class="active"><span><?php echo $gig['Title'] ?></span></li>
    </ul>
  </nav>
<?php
    $img = preg_match ("/^data/",$gig['Img']) ? $gig['Img'] : base64_encode($gig['Img']); 
    
    //user tip
    if (strstr($gig['Type'],'node_')){
      $node_id = str_replace('node_','',$gig['Type']);
      $gig['Link'] = url(drupal_get_path_alias('node/'.$node_id),array('absolute'=> true));
    }

?>
	
  <div class="eventfull clearfix" itemscope itemtype="http://schema.org/Event" data-imgsrc="<?php echo $img ?>">
	  <div class="event-container">
    	<a itemprop="url" target="_blank" href="<?php echo $gig['Link'] ?>" class="eventlink nodisplay">
		  	<span itemprop="name"><?php echo $gig['Title'] ?></span>
		  </a>
        <h1 data-id="<?php echo $gig['Id'] ?>"><?php echo $gig['Title'] ?></h1>
        <h2>
           <span itemprop="startDate" class="date"><?php echo $human_date ?></span>
          
        </h2>
        <h3><a href="<?php echo $location_link ?>"><?php echo $venue['Title'] ?></a> 

            &bull; <a href="<?php echo $prefix .'uitgaan/'.$venue['Cityno'].'-'.rawurlencode($venue['City_name']) ?>" class="city city<?php echo $venue['Cityno'] ?>" data-cityno="<?php echo $venue['Cityno'] ?>"><?php echo $venue['City_name'] ?></a>
            &bull; <span class="country country<?php echo $venue['Countryno'] ?>" data-countryno="<?php echo $venue['Countryno'] ?>"><?php echo t($venue['Country_name']) ?></span> 
 
        </h3>
		<p class="description"><?php echo str_replace('||','<br />',nl2br(trim($gig['Desc']))) ?></p>
		<p><strong>Link</strong>: <a target="_blank" itemprop="url" href="<?php echo $gig['Link'] ?>"><?php echo $gig['Link'] ?></a>  </p>
		<p><i class="icon-info-sign"></i> <a target="_blank" itemprop="url" href="<?php echo $gig['Link'] ?>"><?php echo t('More info') ?> &raquo;</a></p>
  </div>

<?php endif; ?>


<?php if (isset($url_only)): ?>
  <h1 data-id="<?php echo $id ?>"><?php echo $title_tag ?></h1>
  <?php if ($url): ?>
 	  <p class="url_only"><strong>Link</strong>: <a href="<?php echo $url ?>"><?php echo $url ?></a>  </p>
  <?php else: ?>
    
    <p>
       <?php  if ( stristr( $prefix, 'en' ) ): ?>
        The specific event you are trying to find is no longer present in the Muziekladder.nl database.<br> You may find related events on the <a href="/en/muziek">general calendar</a>.
       <?php else: ?>
        Het specifieke evenement dat u zocht is niet langer aanwezig in de Muziekladder.nl database. <br> U kunt gerelateerde evenementen vinden in de <a href="/nl/muziek">algemene agenda</a>.
       <?php endif; ?>
    </p>
  <?php endif; ?>
<?php endif; ?>


<?php if ($venue && !stristr($venue['Title'],'diverse locaties')): ?>
		<div class="location" itemprop="location" itemscope itemtype="http://schema.org/Place">
			<h3><a href="<?php echo $location_link ?>"><span itemprop="name"><?php echo $venue['Title'] ?></span></a></h3>
			<p class="description"><?php echo $venue['Desc'] ?></p>
			<h4><?php echo t('Location') ?>:</h4>
			<p>
				<div> <?php echo $venue['Street'] .' '. $venue['Street_number'] .' '. $venue['Addition'] ?></div> 
				<div itemprop="postalCode"><?php echo $venue['Zip'] ?></div>  
				<div class="city city<?php echo $venue['Cityno'] ?>" data-cityno="<?php echo $venue['Cityno'] ?>"><?php echo $venue['City_name'] ?></div>
                <div class="country country<?php echo $venue['Countryno'] ?>" data-countryno="<?php echo $venue['Countryno'] ?>"><?php echo $venue['Country_name'] ?></div> 
                
			</p>
			<p><a itemprop="url" href="<?php echo $location_link ?>"><i class="icon-info-sign"></i> <?php echo t('More about') ?> <?php echo $venue['Title'] ?></a></p>
		</div>
<?php endif; ?>
 	</div>

