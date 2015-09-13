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
    global $user;
    $lang_url = Muziek_util::lang_url();

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

      // check for multiple dates      
      if (strstr($event_date,',')){
        $date_array = explode(',',$event_date);
      } else {
        $date_array = array($event_date); 
      }
      
      // sort dates
      $timestamp_array = array(); 
      foreach($date_array as $date_value){
        $timestamp_array[strtotime($date_value)] = $date_value; 
      }
      
      ksort ($timestamp_array); 
      $final_date = end($timestamp_array);
      reset ($timestamp_array);
      
      //make sure the last event date is in future 
      $date1 = new DateTime();
      $date2 = new DateTime($final_date);
      $diff = $date2->diff($date1);
      // if the last event date is in the past
      if (($date2 < $date1) && $diff->d > 1){
         // delete it we dont need it
         // if now is a week after the last event date.
         if ((bool)$diff->d && $diff->d > 7){
            rename (MUZIEK_USERDATA_DIR.'/'.$tip,'/tmp/'.$tip);
         } 
         continue; 
      }
   
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

      // turn dates around for display
      $event_date_arr = explode(',',$event_date);
      $display_date_arr = array();
      foreach($event_date_arr as $dty){
        $display_date_arr[strtotime($dty)] = implode('-',array_reverse(explode('-',$dty)));
      }

      ksort($display_date_arr); 
      $display_dates = implode(',',$display_date_arr);
      $proc->setParameter('','event_dates',$display_dates);
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
      $user_name= '';
      $uid = '';
      $user_link = '';
      $edit_link = '';
      $type = $xml->getElementsByTagName('uid');
      if ( $type->item(0) ) {
        $uid = $type->item(0)->nodeValue;
        $userobj = user_load($uid);
        $user_name = $userobj->name;
        $user_link = $lang_url . 'user/'.$uid; 
      }    
      $proc->setParameter('','user',$user_name);
      $proc->setParameter('','uid',$uid);
      $proc->setParameter('','user_link',$user_link);

      // if user is logged in  edit link for their own events
      if ( strlen($uid) && (int)$user->uid && $user->uid == $uid ){
        $edit_link = $lang_url . 'ls';  
      }

      //labels
      $proc->setParameter('','lbl_postdate',t('Posted on'));
      $proc->setParameter('','lbl_soort',t('Type'));
      $proc->setParameter('','lbl_date',t('Date'));
      $proc->setParameter('','lbl_place',t('Place'));
      $proc->setParameter('','lbl_soort',t('Type'));
      $proc->setParameter('','lbl_user',t('Posted by'));

      $proc->registerPHPFunctions();     
       
      $doc = $proc->transformToDoc($xml); 
    
      $html[]= $doc->saveHTML();
    }
    return implode(' ',$html); 
  }
}

