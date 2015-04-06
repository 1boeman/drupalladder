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
      if ($date2 < $date1) continue; 

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
      $venue_id = $venue_ids->item(0)->nodeValue;
      if (strlen($venue_id)){
        $venue_result = $db->get_venue($venue_id);
        $proc->setParameter('','venue_link',$venue_result['Link']);
        $proc->setParameter('','venue_title',$venue_result['Title']);
      }  


      $proc->setParameter('','event_date',$event_date);
      $proc->setParameter('','submit_datetime',date("d/m/Y - h:i:s A",$timestamp));
      $proc->setParameter('','city_name',$city_name);
       
//      $proc->setParameter('','')
      $doc = $proc->transformToDoc($xml); 
    
      $html[]= $doc->saveHTML();
    }
    return implode(' ',$html); 
  }
}

