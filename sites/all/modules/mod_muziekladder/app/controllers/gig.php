<?php

class Gig extends Controller{

  function index(){
    if (!isset($_GET['g'])){
         exit;
     }else{
         $p = $_GET['g'];
    }
    $event_id ='';
    //if (isset ($_GET['id'])) $event_id = $_GET['id'];
    $datestring = $_GET['datestring'];
    $timestamp = strtotime($datestring);
  
    $date_arr =  explode('-',$datestring);
    $year = $date_arr[0];
    $month = $date_arr[1];
    $day = $date_arr[2];
    $weekday = t(date('l',$timestamp));
    $monthname = t(date('F'.$timestamp));
    
    $date = $day.' ' .$monthname. ' '.$year;
    $bodyClass= 'detail';

    $data = Muziek_util::loadGigdata();
    $locationData = Muziek_util::loadLocationIndex();

    $content='';
    $eventlinks = $data->xpath('//day[@date="'.$datestring.'"]/event/link[text()="'.rawurldecode($p).'"]'); 
    if (count ($eventlinks) && $eventlinks){
      $beenhere = false;
      foreach($eventlinks as $link){
        $event = $link->xpath("parent::*"); 
        $event = $event[0];
        if (!$beenhere){
            $beenhere = 1;
            $location = $locationData->xpath('//key[@value="'.$event->src.'"]');
            $location = $location[0]->locationData;
        }
        $img = strlen(trim($event->img)) ? rawurlencode(trim($event->img)) : '';
               
        $countryno = $location->countryno ? $location->countryno : 0;
        
        $countryname = t($this->countrynames_EN[(int)$countryno]);

        $titletag = htmlspecialchars($event->title).' - ';
        $str = theme('gig',array(
            'desc'=>$event->desc,
            'location_title'=>$location->title,
            'city'=>$location->city,
            'cityno'=>$location->cityno,
                    'country'=>$countryname,
                    'countryno'=>$countryno,
            'location_desc'=>$location->desc,
            'zip'=>$location->zip,
            'street'=> $location->street,
            'location_link'=> '/locaties/?l='.rawurlencode($location->link),
            'location_location'=> $location->location,
            'streetnumber'=> $location->streetnumber,
            'streetnumberAddition'=> $location->streetnumberAddition,
            'img'=>$img,
            'link'=>$event->link,
            'title'=>$event->title,               
            'date'=>$weekday.' ' . $date),$this->view->event);        
        $content .=$str;  
        $metadesc = ''; 
        $metadesc .= htmlspecialchars(strip_tags($event->desc));
        $metadesc .= htmlspecialchars($event->title). ' - ';

        $metadesc .= $date . ' - '. $location->title . ' - '. htmlspecialchars($location->city); 
        $titletag .= $date . ' - '. $location->title . ' - '. htmlspecialchars($location->city); 
      }

      $this->set_head_title($titletag);
      # $this->set_title($event->title); 
      return array( 'html' => $str ); 

    
    } else {
      header( "HTTP/1.1 301 Moved Permanently" );
      header( "Status: 301 Moved Permanently" );
      header( "Location: ".rawurldecode($p) );
    }

  }
}
