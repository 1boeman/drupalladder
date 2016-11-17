<?php

class Muziek_util {

  static function can_i_edit($node){
    global $user; 
    if ($node->uid == $user->uid || user_has_role(3)){
      return true; 
    }
    return false; 
  } 

  static function deny (){
    drupal_access_denied();
    module_invoke_all('exit');
    exit();
  }
  
  static function http_link($link){
  // make sure it's a http(s) link 
    if (!preg_match('/^http/i',$link)){
      $link = 'http://'.$link; 
    }
    return $link;
  }
 
  static function saveTipNode($tip_id, $node_id, $uploaded_file){
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
    if (isset($data['venue_select']) && strlen($data['venue_select']) && $data['venue_select'] !== '0'){
      $data['db_venue'] = $db->get_venue($data['venue_select']);
      $data['locatie_link']  = $data['db_venue'] ? self::locatie_link ($data['db_venue']) : false;
    }else{
      $data['venue_select'] = false;
    }
    if (isset($data['city_select']) && (int)$data['city_select']){
      $data['db_city'] = $db->get_city($data['city_select']);
    }else{
      $data['city_select'] = false;
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

    $ewrapper->field_file_id->set($tip_id);

    $ewrapper->body->summary->set($my_body_content_summary);

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

    if($uploaded_file){
      $ewrapper->field_image->file->set($uploaded_file);
    }else{
      $ewrapper->field_image->set(NULL);
    }

    $ewrapper->save();
    $node_id = $ewrapper->getIdentifier();

    return $node_id;
  }

  static function deleteTip ($file_name, $node_id) {
    if ( !preg_match ('/[0-9\-_a-z:]+/',$file_name ) ){
      throw new Exception('gig name probe');
    }

    if ($node_id) node_delete($node_id);

    if( unlink(MUZIEK_USERDATA_DIR.'/'.$file_name) ){
      return 1;
    }
    return 2;
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
    $options = array('absolute' => TRUE);
    $edit_links  = array();
    foreach($tips as $tip){
      // if user is logged in  edit link for their own events
      if ($tip == '.' || $tip == '..' ) continue;
      $xml = new DOMDocument;
      $xml->load(MUZIEK_USERDATA_DIR.'/'.$tip);
      $node_ids = $xml->getElementsByTagName('node_id');

      if( $node_ids->length ) {
        $uid='';
        $type = $xml->getElementsByTagName('uid');
        if ( $type->length && $type->item(0) ) {
          $uid = $type->item(0)->nodeValue;
        }
        $node_id = $node_ids->item(0)->nodeValue;
        if ( strlen($uid) && (int)$user->uid && $user->uid == $uid ){
          $edit_links [$node_id] = $tip;
        }
      }
    }
    return $edit_links;
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
    return self::lang_url().$section.'/'.$db_row['Id'].'-'.rawurlencode(str_replace(' ','_',$db_row['Name']));
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
    $lang_prefix = strlen ($language->prefix) ? base_path().$language->prefix .'/' : base_path();
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
