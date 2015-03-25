<div class="city_gig_agenda" data-page="<?php echo $page ?>" data-count="<?php echo $count ?>" data-rpp="<?php echo $rpp ?>"> 
  <h1><?php echo (strlen ($cityname) ? ucfirst ($cityname) : 'Muziekagenda') ?> - concerten, optredens en evenementen vanaf <?php echo $title_date; ?> </h1>
<?php echo $navigation ?>
<?php if($page > 0): ?>
  <h2 class="pagina">Pagina <?php echo $page+1; ?></h2>
<?php endif; ?>

<div class="page-nav-container top"></div> 
<?php 

$old_date = '';
if(!empty($content)){
  
  foreach($content as $value){
    
    $link = '/gig/?datestring='.$value['Event_Date'].'&g='.rawurlencode($value['Event_Link']); 
    
    if ($old_date != $value['Event_Date']){
      $old_date = $value['Event_Date'];
      $timestamp = strtotime($value['Event_Date']);
      $monthname = t(date("F",$timestamp));
      $dayname = t(date("l",$timestamp));
      $daynumber =  date('d',$timestamp); 
      $monthnumber =  date('m',$timestamp); 
      $human_date = $dayname. ' ' .$daynumber.' '.$monthname. ' ' .date('Y',$timestamp);
      $subhuman_date = substr($dayname,0,2) . ' ' . $daynumber . '/' . $monthnumber;
      echo '<h2 class="human-date">'.$human_date.'</h2>';
    }else{
     // prevent duplications
      if ( $old_title == $value['Event_Title'] &&
          $old_venue == $value['Venue_Title'] ) {
        continue;  
      } 
      $old_title =  $value['Event_Title'];
      $old_venue = $value['Venue_Title'];
       
    }
    ?>                                          
    <div class="city_gig clearfix" itemscope itemtype="http://schema.org/Event" data-imgsrc="<?php echo $value['Event_Img'] ?>">
      <a itemprop="url" href="<?php echo $link ?>">
        <div class="first-cell cell">
          <strong class="name" itemprop="name"><?php echo $value['Event_Title']  ?></strong>
        </div>
        <div class="second-cell cell">
          <span class="city"><?php echo $value['City_Name'] ?>,</span>
   
          <span class="venue"><?php echo $value['Venue_Title'] ?></span>
         <span class="date" itemprop="startDate" content="<?php echo $value['Event_Date'] ?>"><?php echo $subhuman_date ?></span>
        </div>
       </a>
    </div>

  <?php }
}else{ ?>
<p>Niks gevonden helaas.</p>
<p>Probeer een andere stad of dag misschien...</p>
<p>Of als je vindt dat hier iets had moeten staan wat er nu niet staat - gebruik dan het <a href="/muziekformulier">tip-formulier </a>!</p>


<?php    
}
?> 

</div>
<div class="page-nav-container bottom"></div>
