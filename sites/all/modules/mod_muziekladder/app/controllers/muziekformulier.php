<?php

class Muziekformulier extends Controller {

  function __construct() {
  
  }

  function index() {

    $this->set_head_title(t('Muziekladder recommendation'));
    $this->set_title(t('Recommend stuff to the Muziekladder Calendar'));
    $form = drupal_get_form('mod_muziekladder_mailtipform');
    $tips = $this->showTips();
    
    return array('render_array'=>array('muziekform'=>$form,
      'tips'=> array(
        '#type'=>'markup',
        '#markup'=>$tips,
        '#prefix' => '<div class="printed-tips eventfull clearfix">
                        <h3>'.t('Recent recommendations').':</h3>',
        '#suffix' => '</div>',
      )
    )); 
  }

  function showTips(){

    $tips = scandir(MUZIEK_USERDATA_DIR,SCANDIR_SORT_DESCENDING);
    $html = array(); 
    $db = new Muziek_db;  
    foreach($tips as $tip){
      if ($tip == '.' || $tip == '..' ) continue;
      $xsl = new DOMDocument;
      $xsl->load(MUZIEKLADDER_SYSTEM_PATH . '/xsl/tip.xsl');
      $proc = new XSLTProcessor();
      $proc->importStyleSheet($xsl);
      $xml = new DOMDocument;
      $xml->load(MUZIEK_USERDATA_DIR.'/'.$tip);

      $event_dates = $xml->getElementsByTagName('date');
      $event_date = $event_dates->item(0)->nodeValue;
      
      //make sure the event is in future 
      $date1 = new DateTime();
      $date2 = new DateTime($event_date);
      $diff = $date2->diff($date1);

      if (($date2 < $date1) && $diff->d > 1){
         // delete it we dont need it.
         // a week after the event date.
         if ((bool)$diff->d && $diff->d > 7){
            rename (MUZIEK_USERDATA_DIR.'/'.$tip,'/tmp/'.$tip);
         } 
         continue; 
      }

      $event_date = explode('-', $event_date);
      $event_date = array_reverse($event_date);
      $event_date = implode('-', $event_date);
   
      $submit_time = $xml->getElementsByTagname('time');
      $timestamp = $submit_time->item(0)->nodeValue; 
      
      $citynos = $xml->getElementsByTagName('city_select');
      $cityno = $citynos->item(0)->nodeValue; 
      $city_name = '';
      if ((int)$cityno){  
        $city_result = $db->get_city($cityno);
        $city_name = $city_result['Name'];
      }

      $venue_desc = ''; 
      $venue_ids = $xml->getElementsByTagName('venue_select');
      $venue_id =''; 
      if ( $venue_ids->item(0) ) {
        $venue_id = $venue_ids->item(0)->nodeValue;
      }
      if (strlen($venue_id)){
        $venue_result = $db->get_venue($venue_id);
        $proc->setParameter('','venue_link',$venue_result['Link']);
        $proc->setParameter('','venue_title',$venue_result['Title']);
      }  

      $proc->setParameter('','event_date',$event_date);
      $proc->setParameter('','submit_datetime',date("d/m/Y - h:i:s A",$timestamp));
      $proc->setParameter('','city_name',$city_name);

      // soort 
      $soort = '';
      $type = $xml->getElementsByTagName('soort');
      if ( $type->item(0) ) {
        $soort = $type->item(0)->nodeValue;
      }    
      $types = array(
          'concert' => t('Concert or performance'),
          'festival' => t('Festival'),
          'iets_anders' => t('Something else')
      );  
      
      if (isset($types[$soort]) ){
      	 $soort = $types[$soort]; 
      }
      $proc->setParameter('','soort',$soort);

      //user     
      $user= '';
      $uid = '';
      $user_link = ''; 
      $type = $xml->getElementsByTagName('uid');
      if ( $type->item(0) ) {
        $uid = $type->item(0)->nodeValue;
        $userobj = user_load($uid);
        $user = $userobj->name;
        $user_link = Muziek_util::lang_url().'user/'.$uid; 
      }    
      $proc->setParameter('','user',$user);
      $proc->setParameter('','uid',$uid);
      $proc->setParameter('','user_link',$user_link);
      
      //labels
      $proc->setParameter('','lbl_postdate',t('Posted on'));
      $proc->setParameter('','lbl_soort',t('Type'));
      $proc->setParameter('','lbl_date',t('Date'));
      $proc->setParameter('','lbl_place',t('Place'));
      $proc->setParameter('','lbl_soort',t('Type'));
      $proc->setParameter('','lbl_user',t('Posted by'));
      
      $doc = $proc->transformToDoc($xml); 
    
      $html[]= $doc->saveHTML();
    }
    return implode(' ',$html); 
  }
}

