<?php

class Muziek_util {

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

	static function getWeekDay($day,$month,$year){
		$weekdagen = Array(
			'Monday'=>'Maandag','Tuesday'=>'Dinsdag','Wednesday'=>'Woensdag',
			'Thursday'=>'Donderdag','Friday'=>'Vrijdag','Saturday'=>'Zaterdag','Sunday'=>'Zondag');
		return $weekdagen[date("l",strtotime($year.'-'.$month.'-'.$day))];
	}
 // @redo this by xsl -ing a list of cities
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
