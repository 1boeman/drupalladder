<?php 
  $lang_prefix = Muziek_util::lang_url(); 
?>

<div class="city_gig_agenda" data-page="<?php echo $page ?>" data-count="<?php echo $count ?>" data-rpp="<?php echo $rpp ?>"> 
<?php echo $navigation ?>
<?php if($page > 0): ?>
  <h2 class="pagina"> <?php echo t('Page').' '. ($page+1); ?></h2>
<?php endif; ?>

<div class="page-nav-container top"></div> 
<?php 
$old_date = '';
$old_city = ''; 
$old_title = '';
$old_venue = '';
if(!empty($content)){
  
  foreach($content as $value){
    $link = Muziek_util::gig_link($value);  
    
    // block headers
    if ($old_date != $value['Event_Date']){
      $old_date = $value['Event_Date'];
      $hd = Muziek_util::human_date($value['Event_Date']);

      $human_date = $hd['dayname']. ' ' .$hd['daynumber'].' '.$hd['monthname']. ' ' .$hd['year'];
      $subhuman_date = substr($hd['dayname'],0,2) . ' ' . $hd['daynumber'] . '/' . $hd['monthnumber'];
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

    if($old_city != $value['City_Name']){
      $old_city = $value['City_Name'];
      echo '<h3 class="city_header"><a href="'.$lang_prefix.'muziek/'.$value['City_Id'].'-'.$value['City_Name'].'">'.$value['City_Name'].'</a></h3>'; 
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

<p>
<?php 
if ($lang_prefix =='/en/'): ?>
<p>
Sorry, couldn't find any events. 
</p>
<p>
<a href="/en/muziek">
Please select another day or city perhaps.
</a>
</p>
<p>Or if you know something we should have...</p>
<p> Please be so kind as to take a few seconds for our  <a href="/en/muziekformulier">
 recomendation form.
</a>!


</p>


<?php
else: ?>
<p>
Niks gevonden helaas.
</p>
<p>Probeer een andere stad of dag misschien...</p>
<p>Of als je vindt dat hier iets had moeten staan wat er nu niet staat - gebruik dan het <a href="/muziekformulier">tip-formulier </a>!</p>


<?php
  endif;    
}
?> 

</div>
<div class="page-nav-container bottom"></div>
