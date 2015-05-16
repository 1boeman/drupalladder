<?php

class Gig extends Controller{
  
  function index(){
    if (!isset($_GET['g'])){
      drupal_not_found();
      drupal_exit();   
    }
  
    $url = rawurldecode($_GET['g']);
   
    $db  = new Muziek_db(); 
    $gig = false; 
    if (isset( $_GET['id'] )){
      $id = $_GET['id'];
      $gig = $db->get_gig_by_id($id);
    }
    
    if (!$gig && isset($_GET['datestring'])){
      $date = $_GET['datestring']; 
      $gig = $db->get_gig($date,$url);
    }
    
    if (!$gig){
      $this->redirect($url); 
    }
   
    $gig = $gig[0];    
   
    $venue = $db->get_venue($gig['Venue']);
    if (!$venue){
      $this->redirect($url); 
    }

    $prefix =  Muziek_util::lang_url();

    $hd = Muziek_util::human_date($gig['Date']);
    $human_date = $hd['dayname']. ' ' .$hd['daynumber'].' '.$hd['monthname']. ' ' .$hd['year'];

 
    $html = theme('gig',array(
      'gig'=>$gig,
      'venue'=>$venue,
      'prefix'=> $prefix,
      'human_date' => $human_date,
      'location_link'=>$prefix.'locaties/'.$venue['Id'].'-'.rawurlencode($venue['City_name'])
    ));

  #  var_dump($venue);
   # var_dump($gig); 
    $render_array = array(
      'gig'=>array(
        '#type'=>'markup',
        '#markup'=>$html,
      ),
    );
    

    $titletag = htmlspecialchars($gig['Title']) .' - '. htmlspecialchars($venue['Title']) . ' - '. $venue['City_name'] . ' - ' .$human_date; 

    $this->set_head_title($titletag);
    return array('render_array'=>$render_array);

  }
  
  function redirect($url){
      header("HTTP/1.1 303 See Other");
      header( "Location: ".$url );
      exit(); 
  }

/*
  function _index(){
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
    $monthname = t(date('F',$timestamp));
    
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
  */
}
