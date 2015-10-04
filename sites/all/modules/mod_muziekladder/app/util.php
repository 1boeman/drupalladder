<?php

class Muziek_util {

  static function deny (){
    drupal_access_denied(); 
    module_invoke_all('exit');
    exit();   
  }
  
  static function saveTipNode($tip_id,$node_id){
    global $user;
    $db = new Muziek_db;  
 
    $data = self::getTip($tip_id);
    $event_date = $data['date'];
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

    $data['timestamp_array'] = $timestamp_array;
    
    if (isset($data['uid'])){
      $userobj = user_load($data['uid']);
      $data['user_name'] = $userobj->name;
    }

    if (isset($data['venue_select'])){
      $data['db_venue'] = $db->get_venue($data['venue_select']);
      $data['locatie_link']  = $data['db_venue'] ? self::locatie_link ($data['db_venue']) : false;

    }
    
    if (isset($data['city_select']) && (int)$data['city_select']){
      $data['db_city'] = $db->get_city($data['city_select']); 
    }

     $my_body_content = theme('tip_node',array( 'tip' => $data, 'summary' => 0 ));
     $my_body_content_summary = theme('tip_node',array('tip'=>$data ,'summary' => 1));

     
     if ($node_id){
       $entity = entity_load_single('node',$node_id);  
     } else {
       // entity_create replaces the procedural steps in the first example of
       // creating a new object $node and setting its 'type' and uid property
       $values = array(
         'type' => 'article',
         'uid' => $user->uid,
         'status' => 1,
         'comment' => 2,
         'promote' => 1,
       );
   
       $entity = entity_create('node', $values);
     }
     // The entity is now created, but we have not yet simplified use of it.
     // Now create an entity_metadata_wrapper around the new node entity
     // to make getting and setting values easier
     $ewrapper = entity_metadata_wrapper('node', $entity);
     
     // Using the wrapper, we do not have to worry about telling Drupal
     // what language we are using. The Entity API handles that for us.
     $ewrapper->title->set($data['title']);
     
     // Setting the body is a bit different from other properties or fields
     // because the body can have both its complete value and its
     // summary
     # $ewrapper->body->value = 'new value';
     $ewrapper->body->set(array(
      'format' => 'full_html',
      'value' => $my_body_content));
 
   
     #$ewrapper->body->summary->set($my_body_content_summary);
     
     // Setting the value of an entity reference field only requires passing
     // the entity id (e.g., nid) of the entity to which you want to refer
     // The nid 15 here is just an example.
     #$ref_nid = 15;
     // Note that the entity id (e.g., nid) must be passed as an integer not a
     // string
     #$ewrapper->field_my_entity_ref->set(intval($ref_nid));
     
     // Entity API cannot set date field values so the 'old' method must
     // be used
/*     $my_date = new DateTime('January 1, 2013');
     $entity->field_my_date[LANGUAGE_NONE][0] = array(
        'value' => date_format($my_date, 'Y-m-d'),
           'timezone' => 'UTC',
              'timezone_db' => 'UTC',
               );
  */   
     // Now just save the wrapper and the entity
     // There is some suggestion that the 'true' argument is necessary to
     // the entity save method to circumvent a bug in Entity API. If there is
     // such a bug, it almost certainly will get fixed, so make sure to check.
     
     $ewrapper->save(); 
     $node_id = $ewrapper->getIdentifier();
     
     return $node_id; 
  }

  static function getTip ($file_name) {
    if ( !preg_match ('/[0-9\-_a-z:]+/',$file_name ) ){
      throw new Exception('gig name probe');
    }

    $xml = new DOMDocument();
    $data = array(); 
    if( $xml->load(MUZIEK_USERDATA_DIR.'/'.$file_name) ){
      $root = $xml->documentElement;
      foreach($root->childNodes as $node){
        $data[ $node->nodeName ] = $node->nodeValue;
      }
    }
    return $data; 
  } 

  static function showTips(){
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

      // if user is logged in  edit link for their own events
      if ( strlen($uid) && (int)$user->uid && $user->uid == $uid ){
        $edit_link = $lang_url . 'muziekformulier/edit/'.$tip;  
      }

      $proc->setParameter('','user',$user_name);
      $proc->setParameter('','uid',$uid);
      $proc->setParameter('','user_link',$user_link);
      $proc->setParameter('','edit_link',$edit_link);
      $proc->setParameter('','file_name',$tip); 

      //labels
      $proc->setParameter('','lbl_postdate',t('Posted on'));
      $proc->setParameter('','lbl_soort',t('Type'));
      $proc->setParameter('','lbl_date',t('Date'));
      $proc->setParameter('','lbl_place',t('Place'));
      $proc->setParameter('','lbl_soort',t('Type'));
      $proc->setParameter('','lbl_user',t('Posted by'));
      $proc->setParameter('','lbl_edit',t('Edit'));

      $proc->registerPHPFunctions();     
       
      $doc = $proc->transformToDoc($xml); 
    
      $html[]= $doc->saveHTML();
    }
    return implode(' ',$html); 
  }

  static function locatie_link (Array $db_row){
     $lang_prefix = self::lang_url(); 
     return $lang_prefix.'locaties/'.rawurlencode($db_row['Id']).'-'.rawurlencode($db_row['City_name']);
       
  }
  
  static function gig_link(Array $db_row){
    if (isset($db_row['Id'])){
      $id = $db_row['Id'];
      $date = $db_row['Date'];
      $link = rawurlencode($db_row['Link']);  
    }else{
      $id = $db_row['Event_Id'];
      $date = $db_row['Event_Date'];
      $link = rawurlencode($db_row['Event_Link']); 
    }

    $lang_prefix = self::lang_url(); 
    return $lang_prefix.'gig/?id='.$id.'&datestring='.$date.'&g='.$link; 
  }
  
  static function city_link(Array $db_row,$section = 'muziek'){
    return self::lang_url().$section.'/'.$db_row['Id'].'-'.rawurlencode($db_row['Name']);
  } 

  static function human_date($event_date){
     $timestamp = strtotime($event_date); 
     return array(
       'timestamp' => $timestamp,
       'monthname' => t(date("F",$timestamp)),
       'dayname' => t(date("l",$timestamp)),                                                                  
       'daynumber' => date('d',$timestamp),                                                                     
       'monthnumber' =>  date('m',$timestamp),                                                                    
       'year' => date('Y',$timestamp),
     ); 
  }

  static function lang_url(){
    global $language; 
    $lang_prefix = strlen ($language->prefix) ? '/'.$language->prefix .'/' : '/';
    return $lang_prefix;   
  }

  static function shorten ( $string,$number = 25 ){
		$wordarray =  preg_split('/\s+/',$string);
        return implode ( " ", array_slice( $wordarray, 0 , $number ) ); 
	}

  /**** Deprecated : 
  ***** All of the below should be avoided / removed 
  *****/
	static function getWeekDay($day,$month,$year){
		$weekdagen = Array(
			'Monday'=>'Maandag','Tuesday'=>'Dinsdag','Wednesday'=>'Woensdag',
			'Thursday'=>'Donderdag','Friday'=>'Vrijdag','Saturday'=>'Zaterdag','Sunday'=>'Zondag');
		return $weekdagen[date("l",strtotime($year.'-'.$month.'-'.$day))];
	}

  static function getCities(){
		$rv=array(); 
    $data = simplexml_load_file(MUZIEK_DATA_LOCATIONS, 'SimpleXMLElement', LIBXML_NOCDATA);	
    $result = $data->xpath('/cities/city');
    foreach($result as $node){
        $no = (string)$node['cityno'];
        $rv[$no]= array('name'=>(string)$node['name'],'countryno'=>(string)$node['countryno']); 
    }
    return $rv;     
  }

 	static function loadGigdata(){
		return simplexml_load_file(MUZIEK_DATA_GIGS, 'SimpleXMLElement', LIBXML_NOCDATA);
	}

	static function loadLocationIndex(){	
		return simplexml_load_file(MUZIEK_DATA_LOCATION_INDEX, 'SimpleXMLElement', LIBXML_NOCDATA);	
	}

	static function template($valuesArray,$templateString){
		foreach($valuesArray as $key=> $value){
			 $templateString = str_replace('##'.$key.'##',$value,$templateString);		
		}
		
		return $templateString;
	}
}
