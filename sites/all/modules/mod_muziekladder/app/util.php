<?php

class Muziek_util {

	static function shorten ( $string,$number = 25 ){
		$wordarray =  str_word_count($string,2);
		return implode ( " ", array_slice( $wordarray, 0 , $number ) ); 
	}

	static function getWeekDay($day,$month,$year){
		$weekdagen = Array(
			'Monday'=>'Maandag','Tuesday'=>'Dinsdag','Wednesday'=>'Woensdag',
			'Thursday'=>'Donderdag','Friday'=>'Vrijdag','Saturday'=>'Zaterdag','Sunday'=>'Zondag');
		return $weekdagen[date("l",strtotime($year.'-'.$month.'-'.$day))];
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
