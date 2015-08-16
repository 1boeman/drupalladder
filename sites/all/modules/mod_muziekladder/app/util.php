<?php

class Muziek_util {

  static function locatie_link (Array $db_row){
    
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
